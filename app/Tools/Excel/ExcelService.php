<?php

namespace App\Tools\Excel;

use App\Tools\Excel\Interfaces\ExcelInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelService implements ExcelInterface
{
    private array $rows = [];
    private array $header = [];
    private array $body = [];

    public function read($path): ExcelService
    {
        // input normalize
        if (!strpos($path, '.' . 'xlsx')) {
            $path .= '.' . 'xlsx';
        }

        //check Excel file exist
        if (!file_exists($path)) {
            throw new \Exception("File not exists in this path: {$path}");
        }

        $this->rows = \PhpOffice\PhpSpreadsheet\IOFactory::load($path)
            ->getActiveSheet()
            ->toArray();

        $this->fillHeaderAndBody();

        return $this;
    }

    public function rows(): array
    {
        return $this->rows;
    }

    public function records(): array
    {
        //check Excel sheet is set
        if (empty($this->rows)) {
            return [];
        }

        $records = [];
        foreach ($this->body as $row) {
            $records[] = array_combine($this->header, $row);
        }
        return $records;
    }

    public function header(): array
    {
        return $this->header;
    }

    public function body(): array
    {
        return $this->body;
    }

    public function write(string $path, array $data): bool
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
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path));
        }
    }

    private function fillHeaderAndBody(): void
    {
        //check Excel sheet is set
        if (empty($this->rows)) {
            return;
        }

        //get Excel file sheet
        $sheet = $this->rows;

        //first row of Excel file is header
        $this->header = $sheet[0];

        //remove first row of Excel file
        unset($sheet[0]);

        //get all row without header row
        $this->body = $sheet;
    }
}
