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
        Schema::create('development_plans', function (Blueprint $table) {
            $table->id();
            $table->string("subject")
                ->nullable();
            $table->unsignedSmallInteger("progress")
                ->comment("percentage")
                ->nullable();
            $table->unsignedSmallInteger("progress_estimate")
                ->comment("percentage")
                ->nullable();

            $table->dateTime("start_date")
                ->nullable();
            $table->dateTime("estimate_completion")
                ->nullable();

            $table->unsignedBigInteger("financial_period_id")
                ->index("financial_period_id")
                ->nullable();
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
        Schema::dropIfExists('development_plans');
    }
};
