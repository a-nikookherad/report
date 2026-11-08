<?php

namespace App\Services\DataExtractors\BalanceSheets;

use App\Services\DataExtractors\NormalizeAbstract;

class BalanceSheetDataExtractor extends NormalizeAbstract
{
    private const TABLE_NAME_EN = "Balance Sheet";
    private const TABLE_NAME_FA = "صورت وضعيت مالي";
    private const COLUMN_SEQUENCE = 2;
    private static array $fields = [
        "جمع دارايي‌هاي غيرجاري" => [
            "total_non_current_assets",
            100000
        ],
        "جمع دارایی‌های غیرجاری" => [
            "total_non_current_assets",
            100000
        ],
        "دريافتني‌هاي تجاري و ساير دريافتني‌ها" => [
            "trade_and_other_receivables",
            100000
        ],
        "دریافتنی‌های تجاری و سایر دریافتنی‌ها" => [
            "trade_and_other_receivables",
            100000
        ],
        "جمع دارايي‌هاي جاري" => [
            "total_current_assets",
            100000
        ],
        "جمع دارایی‌های جاری" => [
            "total_current_assets",
            100000
        ],
        "جمع دارايي‌ها" => [
            "total_assets",
            100000
        ],
        "جمع دارایی‌ها" => [
            "total_assets",
            100000
        ],
        "سرمايه" => [
            "capital",
            100000
        ],
        "سرمایه" => [
            "capital",
            100000
        ],
        "سود(زيان) انباشته" => [
            "retained_earnings",
            100000
        ],
        "سود (زيان) انباشته" => [
            "retained_earnings",
            100000
        ],
        "جمع حقوق مالکانه" => [
            "total_equity",
            100000
        ],
        "جمع بدهي‌هاي غيرجاري" => [
            "total_non_current_liabilities",
            100000
        ],
        "جمع بدهی‌های غیرجاری" => [
            "total_non_current_liabilities",
            100000
        ],
        "جمع بدهي‌هاي جاري" => [
            "total_current_liabilities",
            100000
        ],
        "جمع بدهی‌های جاری" => [
            "total_current_liabilities",
            100000
        ],
        "جمع بدهي‌ها" => [
            "total_liabilities",
            100000
        ],
        "جمع بدهی‌ها" => [
            "total_liabilities",
            100000
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

        $this->getPublicData();
        $this->getOrder();
        return $this->response;
    }

    private function getOrder(): void
    {
        $this->response["period_order"] = (string)$this->dataSource["period"];
    }

}
