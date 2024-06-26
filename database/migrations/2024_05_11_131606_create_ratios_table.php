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
        Schema::create('ratios', function (Blueprint $table) {
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

//            $table->boolean("fis")
//                ->comment("fis system accept")
//                ->nullable();

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
        Schema::dropIfExists('ratios');
    }
};
