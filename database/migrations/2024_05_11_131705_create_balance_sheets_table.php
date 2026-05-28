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
        Schema::create('balance_sheets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("total_non_current_assets")
                ->comment("جمع دارایی های غیرجاری")
                ->nullable();

            $table->unsignedBigInteger("receivable_claim")
                ->comment("دریافتنی های تجاری")
                ->nullable();

            $table->unsignedBigInteger("trade_and_other_receivables")
                ->comment("دریافتنی های تجاری و سایر دریافتنی ها")
                ->nullable();

            $table->unsignedBigInteger("total_current_assets")
                ->comment("جمع دارایی های جاری")
                ->nullable();

            $table->unsignedBigInteger("total_assets")
                ->comment("جمع دارایی ها")
                ->nullable();

            $table->bigInteger("capital")
                ->comment("سرمایه")
                ->nullable();

            $table->bigInteger("retained_earnings")
                ->comment("سود انباشته")
                ->nullable();

            $table->bigInteger("total_equity")
                ->comment("مجموع حقوق مالکانه")
                ->nullable();

            $table->bigInteger("total_non_current_liabilities")
                ->comment("مجموع بدهی های غیرجاری")
                ->nullable();

            $table->bigInteger("total_current_liabilities")
                ->comment("مجموع بدهی های جاری")
                ->nullable();

            $table->bigInteger("total_liabilities")
                ->comment("مجموع بدهی های")
                ->nullable();

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
            ],"balance_sheets_instrument_financial_period_order_unique");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_sheets');
    }
};
