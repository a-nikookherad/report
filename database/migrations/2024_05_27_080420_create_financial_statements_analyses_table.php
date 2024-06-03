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
        Schema::create('financial_statements_analyses', function (Blueprint $table) {
            $table->id();

            $table->float("gross_profit_percent")
                ->nullable();

            $table->float("net_profit_percent")
                ->nullable();

            $table->float("dividend_percent")
                ->nullable();

            $table->unsignedBigInteger("financial_period_id")
                ->index();
            $table->foreign("financial_period_id")
                ->references("id")->on("financial_periods");

            $table->unsignedSmallInteger("order")
                ->nullable();

            $table->string("net_profit_to_gold")
                ->nullable();
            $table->string("net_profit_year_predict_to_gold")
                ->comment("all this year net profit predict to gold")
                ->nullable();

            $table->string("dividend_to_gold")
                ->nullable();

            $table->unsignedBigInteger("net_profit_year_predict")
                ->comment("all this year net profit predict")
                ->nullable();

            $table->float("a_l")
                ->comment("current assets to current liabilities")
                ->nullable();

            $table->float("rc_a")
                ->comment("receivable claim to asset(%)")
                ->nullable();

            $table->float("roe")
                ->comment("return of equity(%)")
                ->nullable();

            $table->float("roa")
                ->comment("return of asset(%)")
                ->nullable();

            $table->unsignedBigInteger("nav")
                ->comment("net asset value")
                ->nullable();

            $table->string("nav_gold")
                ->comment("net asset value to gold")
                ->nullable();

            $table->integer("peg")
                ->nullable();


            $table->unsignedBigInteger("instrument_id")
                ->index();
            $table->foreign("instrument_id")
                ->references("id")->on("instruments");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_statements_analyses');
    }
};
