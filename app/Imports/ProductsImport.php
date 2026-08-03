<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;

class ProductsImport implements
    ToModel,
    WithBatchInserts,
    WithChunkReading,
    WithColumnLimit,
    WithEvents,
    WithHeadingRow,
    WithLimit,
    WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'name'     => $row['name'],
            'price'    => $row['price'],
        ]);
    }

    public function rules(): array
    {
        return [
            '*.name' => ['required', 'string', 'max:255', 'not_regex:/^\s*=/'],
            '*.price' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
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
