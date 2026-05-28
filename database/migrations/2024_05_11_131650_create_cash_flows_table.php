<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();

            // Operating Activities
            $table->bigInteger('cash_flow_from_operating_activities')
                ->nullable()
                ->comment('نقد حاصل از عمليات');

            $table->bigInteger('cash_paid_for_income_tax')
                ->nullable()
                ->comment('پرداخت‌های نقدی بابت مالیات بر درآمد');

            $table->bigInteger('net_cash_inflow_outflow_from_operating_activities')
                ->nullable()
                ->comment('جريان ‌خالص ‌ورود‌ (خروج) ‌نقد حاصل از فعاليت‌هاي ‌عملياتي');

            // Investing Activities
            $table->bigInteger('cash_received_from_sale_of_property_plant_and_equipment')
                ->nullable()
                ->comment('دريافت‌هاي نقدي حاصل از فروش دارايي‌هاي ثابت مشهود');
            $table->bigInteger('cash_paid_for_purchase_of_property_plant_and_equipment')
                ->nullable()
                ->comment('پرداخت‌هاي نقدي براي خريد دارايي‌هاي ثابت مشهود');
            $table->bigInteger('cash_received_from_sale_of_long_term_investments')
                ->nullable()
                ->comment('دريافت‌هاي نقدي حاصل از فروش سرمايه‌گذاري‌هاي بلندمدت');
            $table->bigInteger('cash_paid_for_acquisition_of_long_term_investments')
                ->nullable()
                ->comment('پرداخت‌هاي نقدي براي تحصيل سرمايه‌گذاري‌هاي بلندمدت');
            $table->bigInteger('cash_received_from_sale_of_short_term_investments')
                ->nullable()
                ->comment('دريافت‌هاي نقدي حاصل از فروش سرمايه‌گذاري‌هاي کوتاه‌مدت');
            $table->bigInteger('cash_paid_for_acquisition_of_short_term_investments')
                ->nullable()
                ->comment('پرداخت‌هاي نقدي براي تحصيل سرمايه‌گذاري‌هاي کوتاه‌مدت');
            $table->bigInteger('cash_received_from_dividends')
                ->nullable()
                ->comment('دريافت‌هاي نقدي حاصل از سود‌ سهام');
            $table->bigInteger('cash_received_from_other_investments')
                ->nullable()
                ->comment('دريافت‌هاي نقدي حاصل از سود ساير سرمايه‌گذاري‌ها');
            $table->bigInteger('net_cash_inflow_outflow_from_investing_activities')
                ->nullable()
                ->comment('جريان خالص ورود (خروج) نقد حاصل از فعاليت‌هاي سرمايه‌گذاري');
            $table->bigInteger('net_cash_before_financing_activities')
                ->nullable()
                ->comment('جريان خالص ورود (خروج) نقد قبل از فعاليت‌هاي تامين مالي');

            // Financing Activities
            $table->bigInteger('cash_received_from_increase_in_capital')
                ->nullable()
                ->comment('دريافت‌هاي نقدي حاصل از افزايش سرمايه');
            $table->bigInteger('cash_received_from_sale_of_treasury_shares')
                ->nullable()
                ->comment('دريافت‌هاي نقدي حاصل از فروش سهام خزانه');
            $table->bigInteger('cash_paid_for_purchase_of_treasury_shares')
                ->nullable()
                ->comment('پرداخت‌هاي نقدي براي خريد سهام خزانه');
            $table->bigInteger('cash_received_from_facilities')
                ->nullable()
                ->comment('دريافت‌هاي نقدي حاصل از تسهيلات');
            $table->bigInteger('cash_paid_for_principal_of_facilities')
                ->nullable()
                ->comment('پرداخت‌هاي نقدي بابت اصل تسهيلات');
            $table->bigInteger('cash_paid_for_interest_of_facilities')
                ->nullable()
                ->comment('پرداخت‌هاي نقدي بابت سود تسهيلات');
            $table->bigInteger('cash_received_from_bonds_issued')
                ->nullable()
                ->comment('دريافت‌هاي نقدي حاصل از انتشار اوراق مشارکت');
            $table->bigInteger('cash_paid_for_principal_of_bonds')
                ->nullable()
                ->comment('پرداخت‌هاي نقدي بابت اصل اوراق مشارکت');
            $table->bigInteger('cash_paid_for_interest_of_bonds')
                ->nullable()
                ->comment('پرداخت‌هاي نقدي بابت سود اوراق مشارکت');
            $table->bigInteger('cash_paid_for_dividends')
                ->nullable()
                ->comment('پرداخت‌هاي نقدي بابت سود سهام');
            $table->bigInteger('net_cash_inflow_outflow_from_financing_activities')
                ->nullable()
                ->comment('جريان خالص ورود (خروج) نقد حاصل از فعاليت‌هاي تامين مالي');

            // Other Information
            $table->string('effect_of_exchange_rate_changes')
                ->nullable()
                ->comment('تاثير تغييرات نرخ ارز');

            $table->bigInteger('net_increase_decrease_in_cash')
                ->nullable()
                ->comment('خالص افزايش (کاهش) در موجودي نقد');
            $table->bigInteger('cash_balance_at_beginning_of_year')
                ->nullable()
                ->comment('مانده موجودي نقد در ابتداي سال');
            $table->bigInteger('cash_balance_at_end_of_year')
                ->nullable()
                ->comment('مانده موجودي نقد در پايان سال');
            $table->bigInteger('non_cash_transactions')
                ->nullable()
                ->comment('معاملات غيرنقدي');

            $table->enum("period_order", [
                3,
                6,
                9,
                12
            ])
                ->nullable();

            $table->string("period_end_date")
                ->comment("دوره عملیاتی")
                ->nullable();

            $table->string("year_end_date")
                ->comment("سال مالی")
                ->nullable();

            $table->unsignedBigInteger("instrument_id")
                ->index();
            $table->foreign("instrument_id")
                ->references("id")
                ->on("instruments");

            $table->unsignedBigInteger("financial_period_id")
                ->index();
            $table->foreign("financial_period_id")
                ->references("id")
                ->on("financial_periods");

            $table->unique([
                'instrument_id',
                'financial_period_id',
                'period_order'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};
