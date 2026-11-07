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
        Schema::create('equities', function (Blueprint $table) {
            $table->id();

            $table->bigInteger("declared_dividend")
                ->comment("سود سهام مصوب")
                ->default(0);

            $table->bigInteger("capital_increase")
                ->comment("افزایش سرمایه")
                ->default(0);

            $table->bigInteger("increase_in_working_capital")
                ->comment("افزایش سرمایه در جریان")
                ->default(0);


            $table->bigInteger("buy_treasury_stock")
                ->comment("خريد سهام خزانه")
                ->default(0);

            $table->bigInteger("sell_treasury_stock")
                ->comment("فروش سهام خزانه")
                ->default(0);

            $table->bigInteger("allocate_to_legal_reserve")
                ->comment("تخصيص به اندوخته قانوني")
                ->default(0);

            $table->bigInteger("allocate_to_other_reserves")
                ->comment("تخصيص به ساير اندوخته‌ها")
                ->default(0);


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
            ],"equities_unique_key");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equities');
    }
};
