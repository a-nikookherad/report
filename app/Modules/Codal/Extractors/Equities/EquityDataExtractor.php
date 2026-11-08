<?php

namespace App\Modules\Codal\Extractors\Equities;

use App\Modules\Codal\Extractors\NormalizeAbstract;

class EquityDataExtractor extends NormalizeAbstract
{
    private const TABLE_NAME_EN = "Changes In Property Rights";
    private const TABLE_NAME_FA = "صورت تغييرات در حقوق مالکانه";
    const UNIT = 100000;
    private static array $fields = [
        "سود سهام مصوب" => [
            "declared_dividend",
            self::UNIT
        ],
        "افزايش سرمايه" => [
            "capital_increase",
            self::UNIT,
            "سرمايه" // filter by specific column
        ],
        "افزايش سرمايه در جريان" => [
            "increase_in_working_capital",
            self::UNIT
        ],
        "خريد سهام خزانه" => [
            "buy_treasury_stock",
            self::UNIT
        ],
        "فروش سهام خزانه" => [
            "sell_treasury_stock",
            self::UNIT
        ],
        "تخصيص به اندوخته قانوني" => [
            "allocate_to_legal_reserve",
            self::UNIT
        ],
        "تخصيص به ساير اندوخته‌ها" => [
            "allocate_to_other_reserves",
            self::UNIT
        ],
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
            $filteredRowSequence = $table->cells
                ->where("value", $item)
                ->last();
            if (empty($filteredRowSequence)) {
                continue;
            }

            if (!empty($fieldAndUnit[2])) {
                $filteredColumn = $table->cells
                    ->where("value", $fieldAndUnit[2])
                    ->first();
                $fieldValue = $table->cells
                    ->where("rowSequence", $filteredRowSequence["rowSequence"])
                    ->where("columnSequence", $filteredColumn["columnSequence"])
                    ->where("valueTypeName","!=","Blank")
                    ->first();
                $this->response[$fieldAndUnit[0]] = $fieldValue["value"] * $fieldAndUnit[1];
                continue;
            }

            $filteredColumns = $table->cells
                ->where("rowSequence", $filteredRowSequence["rowSequence"])
                ->where("valueTypeName", "!=", "Blank");

            $fieldValue = $filteredColumns
                ->slice(1, -1)
                ->reduce(function ($carry, $item) {
                    return $carry + (int)$item["value"];
                }, 0);

            if (empty($fieldValue)) {
                continue;
            }
            $this->response[$fieldAndUnit[0]] = $fieldValue * $fieldAndUnit[1];
        }

        $this->getPublicData();
        $this->response["period_order"] = (string)$this->dataSource["period"];
//        dd($this->response);
        return $this->response;
    }

    public function getDTO(): EquityDTO
    {
        return EquityDTO::fromExtractor($this->response);
    }
}
