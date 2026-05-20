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
        Schema::table('financial_periods', function (Blueprint $table) {
            $table->dropColumn(['instrument_id', 'industry_pe', 'share_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_periods', function (Blueprint $table) {
            $table->unsignedBigInteger("share_count")
                ->comment("تعداد سهام")
                ->nullable();

            $table->float("industry_pe")
                ->comment("price to earn")
                ->nullable();

            $table->unsignedBigInteger("instrument_id")
                ->index();
            $table->foreign("instrument_id")
                ->references("id")
                ->on("instruments");
        });
    }
};
