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
        Schema::create('financial_periods', function (Blueprint $table) {
            $table->id();

            $table->string("solar_start_date")
                ->nullable();

            $table->string("solar_end_date")
                ->nullable();

            $table->date("start_date")
                ->nullable();

            $table->date("end_date")
                ->nullable();

            $table->unsignedBigInteger("share_count")
                ->comment("تعداد سهام")
                ->nullable();

            $table->boolean("close")
                ->comment("وضعیت دوره مالی")
                ->default(false);

            $table->float("industry_pe")
                ->comment("price to earn")
                ->nullable();

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
        Schema::dropIfExists('financial_periods');
    }
};
