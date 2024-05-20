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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("open")
                ->nullable();
            $table->unsignedInteger("high")
                ->nullable();
            $table->unsignedInteger("low")
                ->nullable();
            $table->unsignedInteger("close")
                ->nullable();

            $table->unsignedBigInteger("volume")
                ->nullable();

            $table->unsignedBigInteger("share_count")
                ->nullable();

            $table->string("tarikh")
                ->nullable();

            $table->dateTime("date_time")
                ->nullable()
                ->index("history_date_time_index");

            $table->unsignedBigInteger("timestamp")
                ->index("history_timestamp_index");

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
        Schema::dropIfExists('histories');
    }
};
