<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->integer("gross_profit_percent")
                ->nullable();

            $table->integer("rc_a")
                ->comment("receivable claim to asset(%)")
                ->nullable();

            $table->integer("net_profit_percent")
                ->nullable();

            $table->integer("net_profit")
                ->nullable();

            $table->integer("total_net_profit")
                ->comment("all this year net profit predict")
                ->nullable();

            $table->unsignedBigInteger("nav")
                ->comment("net asset value")
                ->nullable();

            $table->unsignedBigInteger("usd_nav")
                ->comment("nav to dollar")
                ->nullable();

            $table->integer("peg")
                ->nullable();

            $table->integer("roe")
                ->comment("return of equity(%)")
                ->nullable();

            $table->integer("roa")
                ->comment("return of asset(%)")
                ->nullable();

            $table->unsignedBigInteger("instrument_id")
                ->index();
            $table->foreign("instrument_id")
                ->references("id")->on("instruments");

            $table->unsignedBigInteger("financial_period_id")
                ->index();
            $table->foreign("financial_period_id")
                ->references("id")->on("financial_periods");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
