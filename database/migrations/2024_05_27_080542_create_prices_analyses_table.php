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
        Schema::create('prices_analyses', function (Blueprint $table) {
            $table->id();
            $table->float("p_e")
                ->comment("price to earn")
                ->nullable();

            $table->float("p_s")
                ->comment("price to sales")
                ->nullable();

            $table->float("p_a")
                ->comment("price to assets")
                ->nullable();

            $table->float("p_b")
                ->comment("price to book value")
                ->nullable();

            $table->float("p_d")
                ->comment("price to dividend share")
                ->nullable();

            $table->string("p_g")
                ->comment("price to gold")
                ->nullable();

            $table->string("p_f")
                ->comment("price to physical assets")
                ->nullable();

            $table->unsignedSmallInteger("financial_statements_order")
                ->nullable();

            $table->unsignedBigInteger("history_id")
                ->index();
            $table->foreign("history_id")
                ->references("id")
                ->on("histories");

            $table->unsignedBigInteger("activity_id")
                ->nullable();
            $table->foreign("activity_id")
                ->references("id")
                ->on("activities");

            $table->unsignedBigInteger("financial_period_id")
                ->index();
            $table->foreign("financial_period_id")
                ->references("id")
                ->on("financial_periods");

            $table->unsignedBigInteger("instrument_id")
                ->index();
            $table->foreign("instrument_id")
                ->references("id")
                ->on("instruments");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices_analyses');
    }
};
