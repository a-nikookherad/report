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
        Schema::create('income_statements', function (Blueprint $table) {
            $table->id();

            $table->bigInteger("operating_revenue")
                ->comment("درآمدهاي عملياتي")
                ->nullable();

            $table->bigInteger("cost_of_operating_revenue")
                ->comment("بهای تمام شده درآمدهای عملیاتی")
                ->nullable();

            $table->bigInteger("gross_profit_loss")
                ->comment("سود(زیان) ناخالص")
                ->nullable();

            $table->bigInteger("selling_general_administrative_expenses")
                ->comment("هزینه های فروش، ادارى و عمومى")
                ->nullable();

            $table->bigInteger("other_operating_income")
                ->comment("سایر درآمدهای عملیاتی")
                ->nullable();

            $table->bigInteger("other_operating_expenses")
                ->comment("سایر هزینه‌های عملیاتی")
                ->nullable();

            $table->bigInteger("operating_profit_loss")
                ->comment("سود(زیان) عملیاتی")
                ->nullable();

            $table->bigInteger("finance_costs")
                ->comment("هزینه های مالی")
                ->nullable();

            $table->bigInteger("other_non_operating_income_expenses")
                ->comment("سایر درآمدها و هزینه های غیر عملیاتی")
                ->nullable();

            $table->bigInteger("non_operating_investment_income")
                ->comment("سایر درآمدها و هزینه‌های غیرعملیاتی- درآمد سرمایه‌گذاری‌ها")
                ->nullable();

            $table->bigInteger("non_operating_miscellaneous_items")
                ->comment("سایر درآمدها و هزینه‌های غیرعملیاتی- اقلام متفرقه")
                ->nullable();

            $table->bigInteger("current_year_tax")
                ->comment("بدهی مالیات سال جاری")
                ->nullable();

            $table->bigInteger("prior_years_tax")
                ->comment("بدهی مالیات سنوات قبل")
                ->nullable();

            $table->bigInteger("net_profit_loss_from_continuing_operations")
                ->comment("سود(زیان) خالص عملیات مستمر")
                ->nullable();

            $table->bigInteger("net_profit_loss")
                ->comment("سود خالص")
                ->nullable();

            $table->bigInteger("capital")
                ->comment("سرمایه")
                ->nullable();

            $table->enum("period_order", [
                3,
                6,
                9,
                12
            ])
                ->comment("دوره گزارش‌دهی (ماه)")
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
        Schema::dropIfExists('income_statements');
    }
};
