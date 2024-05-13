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
        Schema::create('balance_sheets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("total_current_assets")
                ->comment("مجموع دارایی های جاری")
                ->nullable();

            $table->unsignedBigInteger("receivable_claim")
                ->comment("دریافتنی های تجاری")
                ->nullable();

            $table->unsignedBigInteger("total_assets")
                ->comment("مجموع دارایی های غیرجاری")
                ->nullable();

            $table->unsignedBigInteger("total_current_liabilities")
                ->comment("مجموع بدهی های جاری")
                ->nullable();

            $table->unsignedBigInteger("total_liabilities")
                ->comment("مجموع بدهی های غیرجاری")
                ->nullable();

            $table->unsignedBigInteger("total_equity")
                ->comment("مجموع حقوق مالکانه")
                ->nullable();

            $table->enum("order", [
                1, 2, 3, 4,
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
        Schema::dropIfExists('balance_sheets');
    }
};
