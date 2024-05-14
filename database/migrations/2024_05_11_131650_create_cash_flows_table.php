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

            $table->bigInteger("net_income")
                ->comment("جریان های نقدی حاصل از فعالیت های عملیاتی")
                ->nullable();

            $table->bigInteger("cash_from_investing")
                ->comment("حاصل از فعالیت های سرمایه گذاری")
                ->nullable();

            $table->bigInteger("cash_from_financing")
                ->comment("حاصل فعالیت های تامین مالی")
                ->nullable();

            $table->bigInteger("foreign_exchange_effect")
                ->comment("تسعیر ارز")
                ->nullable();

            $table->unsignedSmallInteger("order")
                ->nullable();

            $table->json("script")
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
