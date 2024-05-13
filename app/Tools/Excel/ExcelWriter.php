<?php

namespace App\Tools\Excel;

use App\Tools\Excel\Interfaces\ExcelWriterInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelWriter implements ExcelWriterInterface
{
    public function write($path, $data)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();
            foreach ($data as $coordinate => $value) {
                $activeWorksheet->setCellValue($coordinate, $value);
            }
            $this->checkOrCreatePath($path);
            $writer = new Xlsx($spreadsheet);
            $writer->save($path);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function checkOrCreatePath(string $path): void
    {
        if (!file_exists($path)) {
            mkdir(dirname($path));
        }
    }
}
