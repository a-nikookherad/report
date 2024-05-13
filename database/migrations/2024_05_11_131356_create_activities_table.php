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

            $table->unsignedBigInteger("domestic_sales")
                ->nullable()
                ->comment("فروش داخلی");

            $table->unsignedBigInteger("export_sales")
                ->nullable()
                ->comment("فروش صادراتی");

            $table->unsignedBigInteger("this_month_sales")
                ->comment("فروش ماه")
                ->nullable();

            $table->unsignedBigInteger("average_sales")
                ->comment("میانگین فروش سال")
                ->nullable();

            $table->unsignedBigInteger("total_domestic_sales")
                ->nullable();

            $table->unsignedBigInteger("total_export_sales")
                ->nullable();

            $table->unsignedBigInteger("total_sales")
                ->comment("کل فروش");

            $table->enum("order", [
                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12
            ])
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
