<?php

namespace App\Modules\Codal\Extractors\Activities;

use App\Services\DataExtractors\NormalizeAbstract;
use App\Tools\PersianDate\PersianDate;

class ActivityDataExtractor extends NormalizeAbstract
{
//    private const TABLE_NAME_EN = "Energy";
//    private const TABLE_NAME_FA = "انرژی";
    private const TABLE_NAME_EN = "Production and sales";
    private const TABLE_NAME_FA = "تولید و فروش";
    private const COLUMN_SEQUENCE = 17;
    private const UNIT = 100000;
    private static array $fields = [
//        "جمع کل"=>1,
        "جمع فروش داخلی" => [
            "domestic_sales",
            self::UNIT
        ],
        "جمع فروش صادراتی" => [
            "export_sales",
            self::UNIT
        ],
        "جمع" => [
            "total_sales",
            self::UNIT
        ]
    ];

    public function __construct($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function extract(): array
    {
        //extract table
        $table = $this->filterTableByName(self::TABLE_NAME_EN, self::TABLE_NAME_FA);

        //extract cells of tables which we need them
        foreach (self::$fields as $item => $fieldAndUnit) {
            $cel = $table->searchRowSequenceByValue($item)
                ->getCelByColumnSequence(self::COLUMN_SEQUENCE);
            if (empty($cel)) {
                continue;
            }
            $this->response[$fieldAndUnit[0]] = $cel["value"] * $fieldAndUnit[1];
        }
        //get order of activity
        $this->getPublicData();
        $this->getOrder();
        return $this->response;
    }

    private function getOrder(): void
    {
        $period = (int)PersianDate::gregorianToSolar($this->dataSource["periodEndToDate"], "m");
        $year = (int)PersianDate::gregorianToSolar($this->dataSource["yearEndToDate"], "m");
        $this->response["period_order"] = (string)(12 - ($year - $period));
    }

    public function getDTO(): ActivityDTO
    {
        return ActivityDTO::fromExtractor($this->response);
    }
}
