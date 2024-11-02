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

            $table->bigInteger("cash_from_operation")
                ->comment("نقد حاصل از عمليات")
                ->nullable();

            $table->bigInteger("cash_from_investing")
                ->comment("نقد حاصل از فعالیت های سرمایه گذاری")
                ->nullable();



            $table->bigInteger("receipts_from_facilities")
                ->comment("دريافت‌هاي نقدي حاصل از تسهيلات")
                ->nullable();

            $table->bigInteger("payments_for_principle_facilities")
                ->comment("پرداخت‌هاي نقدي بابت اصل تسهيلات")
                ->nullable();

            $table->bigInteger("payments_for_interest_facilities")
                ->comment("پرداخت‌هاي نقدي بابت سود تسهيلات")
                ->nullable();

            $table->bigInteger("dividend_payments")
                ->comment("پرداخت‌هاي نقدي بابت سود سهام")
                ->nullable();

            $table->bigInteger("cash_from_financing")
                ->comment("نقد حاصل فعالیت های تامین مالی")
                ->nullable();


            $table->bigInteger("foreign_exchange_effect")
                ->comment("تسعیر ارز")
                ->nullable();

            $table->bigInteger("net_income_cash")
                ->comment("خالص افزايش (کاهش) در موجودي نقد")
                ->nullable();

            $table->unsignedSmallInteger("order")
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

            $table->json("script")
                ->nullable();
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
