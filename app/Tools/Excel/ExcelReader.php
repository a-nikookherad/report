<?php

namespace App\Tools\Excel;

use App\Tools\Excel\Interfaces\ExcelReaderInterface;

class ExcelReader implements ExcelReaderInterface
{
    private array $rows = [];
    private array $header;
    private array $body;

    public function purify(string $excelFileStoragePath, string $extension = null): ExcelReader|\Throwable
    {
        // input normalize
        if (!strpos($excelFileStoragePath, '.' . ($extension ?? 'xlsx'))) {
            $excelFileStoragePath .= '.' . ($extension ?? 'xlsx');
        }

        //check Excel file exist
        $excelFileStoragePath = storage_path($excelFileStoragePath);
        if (!file_exists($excelFileStoragePath)) {
            throw new \Exception("No file exists in this path: {$excelFileStoragePath}");
        }

        $this->rows = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFileStoragePath)
            ->getActiveSheet()
            ->toArray();

        //check Excel sheet is set
        if (empty($this->rows)) {
            return $this;
        }

        $this->getExcelFileHeaderAndBody();

        return $this;
    }

    public function read(): array
    {
        //check Excel sheet is set
        if (empty($this->rows)) {
            return [];
        }

        return [$this->header, $this->body];
    }

    public function rows(): array
    {
        if (empty($this->rows)) {
            return [];
        }

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
        if (empty($this->rows)) {
            return [];
        }

        return $this->header;
    }

    public function body(): array
    {
        if (empty($this->rows)) {
            return [];
        }

        return $this->body;
    }

    private function getExcelFileHeaderAndBody(): void
    {
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
