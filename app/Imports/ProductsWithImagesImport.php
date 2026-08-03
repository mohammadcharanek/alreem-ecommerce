<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;

class ProductsWithImagesImport implements
    ToModel,
    WithBatchInserts,
    WithChunkReading,
    WithColumnLimit,
    WithEvents,
    WithHeadingRow,
    WithLimit,
    WithValidation
{
    protected $imagesFolder;

    public function __construct($imagesFolder = 'products_import_images')
    {
        // Folder inside storage/app/public where images are stored
        $this->imagesFolder = $imagesFolder;
    }

    public function model(array $row)
    {
        // Find category by name or ID if your Excel has category info
        $categoryId = null;
        if (!empty($row['category_id'])) {
            $categoryId = $row['category_id'];
        } elseif (!empty($row['category_name'])) {
            $category = Category::where('name', $row['category_name'])->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }

        // Handle image file from folder
        $imagePath = null;
        if (!empty($row['image'])) {
            $imageFileName = trim($row['image']); // e.g. "product1.jpg"

            // Check if image exists in storage folder
          $publicImagePath = public_path('images/' . $imageFileName);
if (file_exists($publicImagePath)) {
    $imagePath = 'images/' . $imageFileName; // store relative path for blade asset()
}
        }

        return new Product([
            'name'             => $row['name'] ?? 'Unnamed product',
            'description'      => $row['description'] ?? null,
            'price'            => $row['price'] ?? 0,
            'stock'            => $row['stock'] ?? null,
            'brand'            => $row['brand'] ?? null,
            'discount_price'   => $row['discount_price'] ?? null,
            'meta_title'       => $row['meta_title'] ?? null,
            'meta_description' => $row['meta_description'] ?? null,
            'category_id'      => $categoryId,
            'is_active'        => isset($row['is_active']) ? (bool)$row['is_active'] : true,
            'image'            => $imagePath,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.name' => ['required', 'string', 'max:255', 'not_regex:/^\s*=/'],
            '*.description' => ['nullable', 'string', 'max:10000', 'not_regex:/^\s*=/'],
            '*.price' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            '*.discount_price' => ['nullable', 'numeric', 'min:0', 'lt:*.price'],
            '*.stock' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            '*.category_id' => ['nullable', 'integer', 'exists:categories,id'],
            '*.category_name' => ['nullable', 'string', 'max:255', 'not_regex:/^\s*=/'],
            '*.image' => [
                'nullable',
                'string',
                'max:255',
                'not_regex:/^\s*=/',
                'regex:/\A(?!.*\.\.)[A-Za-z0-9][A-Za-z0-9._ -]*\.(?:jpe?g|png|webp)\z/i',
            ],
            '*.meta_title' => ['nullable', 'string', 'max:70', 'not_regex:/^\s*=/'],
            '*.meta_description' => ['nullable', 'string', 'max:500', 'not_regex:/^\s*=/'],
            '*.is_active' => ['nullable', 'boolean'],
        ];
    }

    public function limit(): int
    {
        return 5000;
    }

    public function endColumn(): string
    {
        return 'Z';
    }

    public function chunkSize(): int
    {
        return 250;
    }

    public function batchSize(): int
    {
        return 250;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => static function (BeforeImport $event): void {
                if ($event->reader->getDelegate()->getSheetCount() > 5) {
                    throw new \RuntimeException('The spreadsheet contains too many worksheets.');
                }
            },
        ];
    }
}
