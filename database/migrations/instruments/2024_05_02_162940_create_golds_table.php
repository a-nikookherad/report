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
        Schema::create('golds', function (Blueprint $table) {
            $table->id();
            $table->integer("open")
                ->nullable();
            $table->integer("high")
                ->nullable();
            $table->integer("low")
                ->nullable();
            $table->integer("close")
                ->nullable();
            $table->string("tarikh")->nullable();

            $table->dateTime("date_time")
                ->nullable()
                ->index("gold_date_time_index");
            $table->unsignedBigInteger("timestamp")
                ->index("gold_timestamp_index");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('golds');
    }
};
