<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentExport
{
    protected $formattedData;
    protected $aHead;

    public function __construct($formattedData,$aHead)
    {
        $this->formattedData = $formattedData;
        $this->aHead = $aHead;
    }

    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $activeWorksheet->setCellValue([1, 1], 'Generated Date ' . date('Y-m-d H:i:s'));
        $activeWorksheet->getStyleByColumnAndRow(1, 1)->getFont()->setBold(true);
        
        // Set width for column 1
        $activeWorksheet->getColumnDimensionByColumn(1)->setWidth(50); // Adjust the width as needed

        $dataRowIndex = 3; // Start after the header
        foreach ($this->aHead as $index => $row) {
            $columnIndex = 1;

            foreach ($row as $value) {
                $activeWorksheet->setCellValue([$columnIndex, $dataRowIndex], $value);
                $activeWorksheet->getStyleByColumnAndRow($columnIndex, $dataRowIndex)->getFont()->setBold(true);
                $activeWorksheet->getColumnDimensionByColumn($columnIndex)->setWidth(20);
                $columnIndex++;
            }

            $dataRowIndex++;
        }

        // Assuming you want to set the data in the Excel file

        foreach ($this->formattedData as $index => $row) {
            $columnIndex = 1;

            foreach ($row as $value) {
                $activeWorksheet->setCellValue([$columnIndex, $dataRowIndex], $value);
                $columnIndex++;
            }

            $dataRowIndex++;
        }

        $writer = new Xlsx($spreadsheet);

        $fileName = date('Y-m-d H:i:s').'_'.'studentInfo.xlsx';

        return new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="' . $fileName . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
}
