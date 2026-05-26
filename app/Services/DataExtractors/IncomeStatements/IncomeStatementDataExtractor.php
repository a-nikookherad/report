<?php

namespace App\Services\DataExtractors\IncomeStatements;

use App\Services\DataExtractors\NormalizeAbstract;

class IncomeStatementDataExtractor extends NormalizeAbstract
{
    private const TABLE_NAME_EN = "Income Statement";
    private const TABLE_NAME_FA = "صورت سود و زيان";
    private const COLUMN_SEQUENCE = 2;
    private static array $fields = [
        "درآمدهاي عملياتي" => [
            "operating_revenue",
            100000
        ],
        "درآمدهای عملیاتی" => [
            "operating_revenue",
            100000
        ],
        "بهاى تمام شده درآمدهاي عملياتي" => [
            "cost_of_operating_revenue",
            100000
        ],
        "بهاى تمام شده درآمدهای عملیاتی" => [
            "cost_of_operating_revenue",
            100000
        ],
        "سود(زيان) ناخالص" => [
            "gross_profit_loss",
            100000
        ],
        "سود (زيان) ناخالص" => [
            "gross_profit_loss",
            100000
        ],
        "هزينه‏ هاى فروش، ادارى و عمومى" => [
            "selling_general_administrative_expenses",
            100000
        ],
        "هزينه‏‌هاى فروش، ادارى و عمومى" => [
            "selling_general_administrative_expenses",
            100000
        ],
        "ساير درآمدها" => [
            "other_operating_income",
            100000
        ],
        "ساير هزينه‌ها" => [
            "other_operating_expenses",
            100000
        ],
        "سایر درآمدها و هزینه‌های عملیاتی" => [
            "other_operating_expenses",
            100000
        ],
        "سود(زيان) عملياتى" => [
            "operating_profit_loss",
            100000
        ],
        "سود (زيان) عملياتي" => [
            "operating_profit_loss",
            100000
        ],
        "هزينه‏‌هاى مالى" => [
            "finance_costs",
            100000
        ],
        "هزينه‏ هاى مالى" => [
            "finance_costs",
            100000
        ],
        "ساير درآمدها و هزينه ‏هاى غيرعملياتى" => [
            "other_non_operating_income_expenses",
            100000
        ],
        "سایر درآمدها و هزینه‌های غیرعملیاتی- درآمد سرمایه‌گذاری‌ها" => [
            "non_operating_investment_income",
            100000
        ],
        "سایر درآمدها و هزینه‌های غیرعملیاتی- اقلام متفرقه" => [
            "non_operating_miscellaneous_items",
            100000
        ],
        "سال جاری" => [
            "current_year_tax",
            100000
        ],
        "سال جاري" => [
            "current_year_tax",
            100000
        ],
        "سال‌هاي قبل" => [
            "prior_years_tax",
            100000
        ],
        "سود(زيان) خالص عمليات در حال تداوم" => [
            "net_profit_loss_from_continuing_operations",
            100000
        ],
        "سود(زيان) خالص" => [
            "net_profit_loss",
            100000
        ],
        "سود (زيان) خالص" => [
            "net_profit_loss",
            100000
        ],
        "سرمايه" => [
            "capital",
            100000
        ],
        "سرمایه" => [
            "capital",
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

//        dd(1,$table);
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
//        dd("new",$this->response);
        return $this->response;
    }

    private function getOrder(): void
    {
        $this->response["period_order"] = $this->dataSource["period"];
    }
}
