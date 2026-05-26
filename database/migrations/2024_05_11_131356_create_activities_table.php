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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("energy_cost")
                ->nullable()
                ->comment("هزینه انرژی ماه جاری");

            $table->unsignedBigInteger("feed_cost")
                ->nullable()
                ->comment("هزینه خوراک ماه جاری");

            $table->unsignedBigInteger("domestic_sales")
                ->nullable()
                ->comment("فروش داخلی ماه جاری");

            $table->unsignedBigInteger("export_sales")
                ->nullable()
                ->comment("فروش صادراتی ماه جاری");

            $table->unsignedBigInteger("total_sales")
                ->nullable()
                ->comment("فروش ماه جاری");

            $table->enum("order", [
                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12
            ])
                ->nullable();

            $table->date("period_end_date")
                ->comment("دوره عملیاتی")
                ->nullable();

            $table->date("year_end_date")
                ->comment("سال مالی")
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
        Schema::dropIfExists('activities');
    }
};
