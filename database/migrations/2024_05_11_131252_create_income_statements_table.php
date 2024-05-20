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
                ->comment("هزینه های فروش، ادارى و عمومى")
                ->nullable();

            $table->bigInteger("other_operating_income")
                ->comment("سایر درآمدهای عملیاتی")
                ->nullable();

            $table->bigInteger("operating_income")
                ->comment("سود عملیاتی")
                ->nullable();

            $table->bigInteger("financial_cost")
                ->comment("هزینه های مالی")
                ->nullable();

            $table->bigInteger("other_income")
                ->comment("سایر درآمدهای غیر عملیاتی")
                ->nullable();

            $table->bigInteger("tax")
                ->comment("مالیات بر درآمد")
                ->nullable();

            $table->bigInteger("net_income")
                ->comment("سود خالص")
                ->nullable();

            $table->bigInteger("fund")
                ->comment("سرمایه")
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
