<?php

namespace Tests\Feature\Security;

use App\Imports\ProductsImport;
use App\Imports\ProductsWithImagesImport;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Events\BeforeImport;
use Tests\TestCase;

class ImportSafetyTest extends TestCase
{
    public function test_import_workload_is_bounded(): void
    {
        foreach ([new ProductsImport, new ProductsWithImagesImport] as $import) {
            $this->assertSame(5000, $import->limit());
            $this->assertSame('Z', $import->endColumn());
            $this->assertSame(250, $import->chunkSize());
            $this->assertSame(250, $import->batchSize());
            $this->assertArrayHasKey(BeforeImport::class, $import->registerEvents());
        }
    }

    public function test_basic_import_rejects_formula_values(): void
    {
        $validator = Validator::make([
            ['name' => '=WEBSERVICE("https://attacker.test")', 'price' => 10],
        ], (new ProductsImport)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('0.name', $validator->errors()->toArray());
    }

    public function test_image_import_rejects_formulas_urls_and_path_traversal(): void
    {
        $rules = (new ProductsWithImagesImport)->rules();

        foreach ([
            '=HYPERLINK("https://attacker.test")',
            'https://attacker.test/image.jpg',
            '../image.jpg',
            'payload.php',
        ] as $unsafeImage) {
            $validator = Validator::make([
                [
                    'name' => 'Valid product',
                    'price' => 10,
                    'image' => $unsafeImage,
                ],
            ], $rules);

            $this->assertTrue($validator->fails(), $unsafeImage);
            $this->assertArrayHasKey('0.image', $validator->errors()->toArray());
        }
    }
}
