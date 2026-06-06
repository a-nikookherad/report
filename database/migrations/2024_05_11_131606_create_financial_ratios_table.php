<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_ratios', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('instrument_id')->index();
            $table->foreign('instrument_id')
                ->references('id')
                ->on('instruments')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('financial_period_id')
                ->nullable()
                ->index();

            $table->foreign('financial_period_id')
                ->references('id')
                ->on('financial_periods')
                ->nullOnDelete();

            $table->enum('period_order', [3, 6, 9, 12])
                ->nullable();

            $table->string('ratio_key');
            // examples:
            // roa
            // roe
            // pe
            // pb
            // ps
            // current_ratio
            // debt_to_equity

            $table->decimal('ratio_value', 20, 6)
                ->nullable();

            $table->string('period_end_date')
                ->nullable();

            $table->string('year_end_date')
                ->nullable();

            $table->timestamp('calculated_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'instrument_id',
                'financial_period_id',
                'period_order',
                'ratio_key'
            ], 'financial_ratio_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_ratios');
    }
};
