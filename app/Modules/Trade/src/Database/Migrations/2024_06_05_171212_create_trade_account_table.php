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
        Schema::create('trade_account', function (Blueprint $table) {
            $table->id();
            $table->string("instrument_name")
                ->nullable();
            $table->float("total_amount")
                ->nullable();

            $table->float("backup_amount")
                ->comment("20 percent of fund")
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_account');
    }
};
