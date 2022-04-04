<?php

use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use App\ProductType;

class ProductTypesFromExcelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fileExtension = "Xlsx";
        $inputFileName = storage_path('app/private/data_import/fiber-product_types.xlsx');

        // Reader
        $reader = IOFactory::createReader($fileExtension);
        $reader->setReadDataOnly(true);

        // Load SpreadSheet
        $spreadsheet = $reader->load($inputFileName);
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheetData = $worksheet->toArray();

       // Remove first row
        array_shift($worksheetData);

       // Format the array
        foreach ($worksheetData as $rowDataKey => $rowDataArrayValue) {
            $worksheetData[$rowDataKey]['type'] = $rowDataArrayValue[0];
            $worksheetData[$rowDataKey]['created_at'] = now();
            $worksheetData[$rowDataKey]['updated_at'] = now();

            unset($worksheetData[$rowDataKey][0]);
        }

        ProductType::insert($worksheetData);
    }
}
