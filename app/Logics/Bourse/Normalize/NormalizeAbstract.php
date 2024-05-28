<?php

namespace App\Logics\Bourse\Normalize;

abstract class NormalizeAbstract
{
    protected $dataSource;
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

    protected function prepareData(): object
    {
        foreach ($this->dataSource["sheets"][0]["tables"][0]["cells"] as $cel) {
            $this->data[$cel["address"]] = $cel["value"];
        }
        $this->data["yearEndToDate"] = $this->dataSource["yearEndToDate"];
        $this->data["periodEndToDate"] = $this->dataSource["periodEndToDate"];
        return $this;
    }

    abstract public function getData(): array;

    abstract public function normalize(): array;
}
