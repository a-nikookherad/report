<?php

namespace App\Logics\ImportData;

use App\Tools\Excel\ExcelService;
use App\Tools\Excel\Interfaces\ExcelReaderInterface;

class ImportInstrumentDataLogic
{
    /**
     * @var $excelReader ExcelService
     */
    private $excelReader = null;

    public function __construct()
    {
        $this->excelReader = resolve(ExcelReaderInterface::class);
    }

    public function getBalanceSheet($storagePath, $extension)
    {
        $file = $this->excelReader->path($storagePath, $extension);
        $records = $file->records();
        return $records;
    }

    public function getIncomeStatement()
    {

    }

    public function getCashFlow()
    {

    }

    public function getActivities()
    {

    }


}
