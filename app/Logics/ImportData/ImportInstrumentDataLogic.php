<?php

namespace App\Logics\ImportData;

use App\Tools\Excel\ExcelReader;
use App\Tools\Excel\Interfaces\ExcelReaderInterface;

class ImportInstrumentDataLogic
{
    /**
     * @var $excelReader ExcelReader
     */
    private $excelReader = null;

    public function __construct()
    {
        $this->excelReader = resolve(ExcelReaderInterface::class);
    }

    public function getBalanceSheet($storagePath, $extension)
    {
        $file = $this->excelReader->purify($storagePath, $extension);
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
