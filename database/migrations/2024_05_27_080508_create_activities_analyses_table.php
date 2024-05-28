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
        Schema::create('activities_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("total_year_sales")
                ->comment("فروش کل سال")
                ->nullable();

            $table->unsignedBigInteger("predict_year_sales")
                ->comment("پیش بینی فروش کل سال")
                ->nullable();

            $table->unsignedBigInteger("season_sales")
                ->comment("کل فروش در فصل")
                ->nullable();

            $table->unsignedBigInteger("season_sales_predict")
                ->comment("پیش بینی فروش فصل")
                ->nullable();

            $table->enum("order", [
                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12
            ])
                ->nullable();

            $table->unsignedBigInteger("activity_id")
                ->index();
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
        Schema::dropIfExists('activities_analyses');
    }
};
