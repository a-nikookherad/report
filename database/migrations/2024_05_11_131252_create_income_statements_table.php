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
            $table->bigInteger("total_revenue")
                ->comment("درآمد کل")
                ->nullable();

            $table->bigInteger("cost_of_revenue")
                ->comment("بهای تمام شده")
                ->nullable();

            $table->bigInteger("gross_profit")
                ->comment("سود ناخالص")
                ->nullable();

            $table->bigInteger("operation_expenses")
                ->comment("هزینه عملیاتی")
                ->nullable();

            $table->bigInteger("operating_income")
                ->comment("درآمد عملیاتی")
                ->nullable();

            $table->bigInteger("other_income")
                ->comment("سایر درآمدهای عملیاتی")
                ->nullable();

            $table->bigInteger("net_income")
                ->comment("درآمد خالص")
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
        Schema::dropIfExists('income_statements');
    }
};
