<?php

namespace App\Services\DataExtractors;

use Illuminate\Support\Collection;

abstract class NormalizeAbstract
{
    protected array $dataSource;
    private array $table;

    //todo deprecate code
    protected $data;
    protected $cols = [
        "B",
        "C",
        "D",
        "E",
        "F",
        "G",
        "H",
        "I",
        "J",
        "K",
        "L",
        "M",
        "N",
        "O",
        "P",
        "Q",
        "R",
        "S",
        "T",
        "U",
        "V",
        "W",
        "X",
        "Y",
        "Z",
    ];


    public function filterTableByName(string $tableNameEn = null, string $tableNameFa = null)
    {
        foreach ($this->dataSource["sheets"][0]["tables"] as $table) {
            if (($table["title_Fa"] == $tableNameFa || $table["title_En"] == $tableNameEn)) {
                $this->table = $table;
            }
        }
        return $this;
    }

    public function getCelByValue(string $value)
    {
        $cells = collect($this->table["cells"]);
        $rowSequence = $cells
            ->where("value", $value)
            ->first()["rowSequence"];

        $collect = $cells
            ->where("valueTypeName", "!=", "Blank")
            ->where("value", "!=", "0")
            ->where("rowSequence", $rowSequence)
            ->where("yearEndToDate", $this->dataSource["yearEndToDate"])
            ->where("periodEndToDate", $this->dataSource["periodEndToDate"]);
//        dd($collect);
        if ($collect->count() == 4) {
            return $collect->skip(1)
                ->first();
        }
        return $collect->skip(0)
            ->first();
    }

    protected function prepareData(string $tableNameEn, string $tableNameFa = null): object
    {
        foreach ($this->dataSource["sheets"][0]["tables"] as $table) {
            if (
                ($table["title_Fa"] == $tableNameFa || $table["title_En"] == $tableNameEn)
            ) {
//                dd($table["cells"]);
                foreach ($table["cells"] as $cel) {
                    /*if ($cel["isVisible"] == true) {
                        $this->data[$cel["address"]] = $cel["value"];
                    }*/
                    $this->data[$cel["address"]] = $cel["value"];
                }
            }
        }
        $this->data["yearEndToDate"] = $this->dataSource["yearEndToDate"];
        $this->data["periodEndToDate"] = $this->dataSource["periodEndToDate"];
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
