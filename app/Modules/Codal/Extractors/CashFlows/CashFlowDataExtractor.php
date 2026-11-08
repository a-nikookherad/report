<?php

namespace App\Modules\Codal\Extractors\CashFlows;

use App\Modules\Codal\Extractors\NormalizeAbstract;

class CashFlowDataExtractor extends NormalizeAbstract
{
    private const TABLE_NAME_EN = "Cash Flow";
    private const TABLE_NAME_FA = "صورت جریان های نقدی";
    private const COLUMN_SEQUENCE = 2;
    private const UNITE = 100000;
    private static array $fields = [
        "نقد حاصل از عمليات" => [
            "cash_flow_from_operating_activities",
            self::UNITE
        ],
        "نقد حاصل از عملیات" => [
            "cash_flow_from_operating_activities",
            self::UNITE
        ],
        "پرداخت‌هاي نقدي بابت ماليات بر درآمد" => [
            "cash_paid_for_income_tax",
            self::UNITE
        ],
        "جريان ‌خالص ‌ورود‌ (خروج) ‌نقد حاصل از فعاليت‌هاي ‌عملياتي" => [
            "net_cash_inflow_outflow_from_operating_activities",
            self::UNITE
        ],
        "دريافت‌هاي نقدي حاصل از فروش دارايي‌هاي ثابت مشهود" => [
            "cash_received_from_sale_of_property_plant_and_equipment",
            self::UNITE
        ],
        "پرداخت‌هاي نقدي براي خريد دارايي‌هاي ثابت مشهود" => [
            "cash_paid_for_purchase_of_property_plant_and_equipment",
            self::UNITE
        ],
        "دريافت‌هاي نقدي حاصل از فروش سرمايه‌گذاري‌هاي بلندمدت" => [
            "cash_received_from_sale_of_long_term_investments",
            self::UNITE
        ],
        "پرداخت‌هاي نقدي براي تحصيل سرمايه‌گذاري‌هاي بلندمدت" => [
            "cash_paid_for_acquisition_of_long_term_investments",
            self::UNITE
        ],
        "دريافت‌هاي نقدي حاصل از فروش سرمايه‌گذاري‌هاي کوتاه‌مدت" => [
            "cash_received_from_sale_of_short_term_investments",
            self::UNITE
        ],
        "پرداخت‌هاي نقدي براي تحصيل سرمايه‌گذاري‌هاي کوتاه‌مدت" => [
            "cash_paid_for_acquisition_of_short_term_investments",
            self::UNITE
        ],
        "دريافت‌هاي نقدي حاصل از سود‌ سهام" => [
            "cash_received_from_dividends",
            self::UNITE
        ],
        "دريافت‌هاي نقدي حاصل از سود ساير سرمايه‌گذاري‌ها" => [
            "cash_received_from_other_investments",
            self::UNITE
        ],
        "جريان خالص ورود (خروج) نقد حاصل از فعاليت‌هاي سرمايه‌گذاري" => [
            "net_cash_inflow_outflow_from_investing_activities",
            self::UNITE
        ],
        "جريان خالص ورود (خروج) نقد حاصل از فعاليت‌های سرمایه‌گذاری" => [
            "net_cash_inflow_outflow_from_investing_activities",
            self::UNITE
        ],
        "جريان خالص ورود (خروج) نقد قبل از فعاليت‌هاي تامين مالي" => [
            "net_cash_before_financing_activities",
            self::UNITE
        ],//تامین مالی
        "دريافت‌هاي نقدي حاصل از افزايش سرمايه" => [
            "cash_received_from_increase_in_capital",
            self::UNITE
        ],
        "دريافت‌هاي نقدي حاصل از فروش سهام خزانه" => [
            "cash_received_from_sale_of_treasury_shares",
            self::UNITE
        ],
        "پرداخت‌هاي نقدي براي خريد سهام خزانه" => [
            "cash_paid_for_purchase_of_treasury_shares",
            self::UNITE
        ],
        "دريافت‌هاي نقدي حاصل از تسهيلات" => [
            "cash_received_from_facilities",
            self::UNITE
        ],
        "دریافت‌های نقدی حاصل از تسهيلات" => [
            "cash_received_from_facilities",
            self::UNITE
        ],
        "پرداخت‌هاي نقدي بابت اصل تسهيلات" => [
            "cash_paid_for_principal_of_facilities",
            self::UNITE
        ],
        "پرداخت‌های نقدی بابت اصل تسهيلات" => [
            "cash_paid_for_principal_of_facilities",
            self::UNITE
        ],
        "پرداخت‌هاي نقدي بابت سود تسهيلات" => [
            "cash_paid_for_interest_of_facilities",
            self::UNITE
        ],
        "پرداخت‌های نقدی بابت سود تسهيلات" => [
            "cash_paid_for_interest_of_facilities",
            self::UNITE
        ],
        "دريافت‌هاي نقدي حاصل از انتشار اوراق مشارکت" => [
            "cash_received_from_bonds_issued",
            self::UNITE
        ],
        "پرداخت‌هاي نقدي بابت اصل اوراق مشارکت" => [
            "cash_paid_for_principal_of_bonds",
            self::UNITE
        ],
        "پرداخت‌هاي نقدي بابت سود اوراق مشارکت" => [
            "cash_paid_for_interest_of_bonds",
            self::UNITE
        ],
        "پرداخت‌هاي نقدي بابت سود سهام" => [
            "cash_paid_for_dividends",
            self::UNITE
        ],
        "پرداخت‌های نقدی بابت سود سهام" => [
            "cash_paid_for_dividends",
            self::UNITE
        ],
        "جريان خالص ورود (خروج) نقد حاصل از فعاليت‌هاي تامين مالي" => [
            "net_cash_inflow_outflow_from_financing_activities",
            self::UNITE
        ],
        "جريان خالص ورود (خروج) نقد حاصل از فعالیت‌های تامين مالی" => [
            "net_cash_inflow_outflow_from_financing_activities",
            self::UNITE
        ],
        "تاثير تغييرات نرخ ارز" => [
            "effect_of_exchange_rate_changes",
            self::UNITE
        ],
        "خالص افزايش (کاهش) در موجودي نقد" => [
            "net_increase_decrease_in_cash",
            self::UNITE
        ],
        "خالص افزايش (کاهش) در موجودی نقد" => [
            "net_increase_decrease_in_cash",
            self::UNITE
        ],
        "مانده موجودي نقد در ابتداي سال" => [
            "cash_balance_at_beginning_of_year",
            self::UNITE
        ],
        "مانده موجودي نقد در پايان سال" => [
            "cash_balance_at_end_of_year",
            self::UNITE
        ],
        "معاملات غيرنقدي" => [
            "non_cash_transactions",
            self::UNITE
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

//        dd(1, $table);
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

    public function getDTO(): CashFlowDTO
    {
        return CashFlowDTO::fromExtractor($this->response);
    }
}
