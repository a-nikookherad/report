<?php

namespace App\Modules\Codal\Extractors\IncomeStatements;

use App\Modules\Codal\Extractors\NormalizeAbstract;

class IncomeStatementDataExtractor extends NormalizeAbstract
{
    private const TABLE_NAME_EN = "Income Statement";
    private const TABLE_NAME_FA = "صورت سود و زيان";
    private const COLUMN_SEQUENCE = 2;
    const UNIT = 100000;
    private static array $fields = [
        "درآمدهاي عملياتي" => [
            "operating_revenue",
            self::UNIT
        ],
        "درآمدهای عملیاتی" => [
            "operating_revenue",
            self::UNIT
        ],
        "بهاى تمام شده درآمدهاي عملياتي" => [
            "cost_of_operating_revenue",
            self::UNIT
        ],
        "بهاى تمام شده درآمدهای عملیاتی" => [
            "cost_of_operating_revenue",
            self::UNIT
        ],
        "سود(زيان) ناخالص" => [
            "gross_profit_loss",
            self::UNIT
        ],
        "سود (زيان) ناخالص" => [
            "gross_profit_loss",
            self::UNIT
        ],
        "هزينه‏ هاى فروش، ادارى و عمومى" => [
            "selling_general_administrative_expenses",
            self::UNIT
        ],
        "هزينه‏‌هاى فروش، ادارى و عمومى" => [
            "selling_general_administrative_expenses",
            self::UNIT
        ],
        "ساير درآمدها" => [
            "other_operating_income",
            self::UNIT
        ],
        "ساير هزينه‌ها" => [
            "other_operating_expenses",
            self::UNIT
        ],
        "سایر درآمدها و هزینه‌های عملیاتی" => [
            "other_operating_expenses",
            self::UNIT
        ],
        "سود(زيان) عملياتى" => [
            "operating_profit_loss",
            self::UNIT
        ],
        "سود (زيان) عملياتي" => [
            "operating_profit_loss",
            self::UNIT
        ],
        "هزينه‏‌هاى مالى" => [
            "finance_costs",
            self::UNIT
        ],
        "هزينه‏ هاى مالى" => [
            "finance_costs",
            self::UNIT
        ],
        "ساير درآمدها و هزينه ‏هاى غيرعملياتى" => [
            "other_non_operating_income_expenses",
            self::UNIT
        ],
        "سایر درآمدها و هزینه‌های غیرعملیاتی- درآمد سرمایه‌گذاری‌ها" => [
            "non_operating_investment_income",
            self::UNIT
        ],
        "سایر درآمدها و هزینه‌های غیرعملیاتی- اقلام متفرقه" => [
            "non_operating_miscellaneous_items",
            self::UNIT
        ],
        "سال جاری" => [
            "current_year_tax",
            self::UNIT
        ],
        "سال جاري" => [
            "current_year_tax",
            self::UNIT
        ],
        "سال‌هاي قبل" => [
            "prior_years_tax",
            self::UNIT
        ],
        "سود(زيان) خالص عمليات در حال تداوم" => [
            "net_profit_loss_from_continuing_operations",
            self::UNIT
        ],
        "سود(زيان) خالص" => [
            "net_profit_loss",
            self::UNIT
        ],
        "سود (زيان) خالص" => [
            "net_profit_loss",
            self::UNIT
        ],
        "سرمايه" => [
            "capital",
            self::UNIT
        ],
        "سرمایه" => [
            "capital",
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
        $this->response["period_order"] = (string)$this->dataSource["period"];
    }

    public function getDTO(): IncomeStatementDTO
    {
        return IncomeStatementDTO::fromExtractor($this->response);
    }
}
