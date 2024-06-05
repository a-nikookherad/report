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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->float("fund")
                ->nullable();
            $table->float("risk")
                ->nullable();
            $table->enum("type", [
                "real",
                "forward test",
                "back test",
            ])->default("real");
            $table->unsignedBigInteger("strategy_id")
                ->nullable();

            $table->unsignedSmallInteger("win_rate")
                ->nullable();
            $table->unsignedSmallInteger("loss_rate")
                ->nullable();

            $table->float("pay_of_ratio")
                ->nullable();

            $table->float("profit_factor")
                ->nullable();

            $table->float("expectancy")
                ->nullable();

            $table->dateTime("financial_period_started_at")
                ->nullable();
            $table->dateTime("financial_period_ended_at")
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
