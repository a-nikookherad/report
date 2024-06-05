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
        Schema::create('trade_journals', function (Blueprint $table) {
            $table->id();

            $table->string("instrument_name")
                ->nullable();

            $table->float("open_price")
                ->nullable();

            $table->float("close_price")
                ->nullable();

            $table->string("reason_for_open_order")
                ->nullable();
            $table->string("reason_for_close_order")
                ->nullable();

            $table->string("emotion")
                ->nullable();

            $table->dateTime("started_at")
                ->nullable();

            $table->dateTime("ended_at")
                ->nullable();


            $table->string("solar_started_at")
                ->nullable();

            $table->string("solar_ended_at")
                ->nullable();

            $table->unsignedBigInteger("trade_id");
            $table->foreign("trade_id")
                ->references("id")
                ->on("trades");
            $table->string("tarikh")
                ->nullable();
            $table->dateTime("date_time");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_journals');
    }
};
