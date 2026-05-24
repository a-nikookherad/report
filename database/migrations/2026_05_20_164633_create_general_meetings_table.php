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
        Schema::create('general_meetings', function (Blueprint $table) {
            $table->id();
            $table->enum("type", [
                "annual",//مجمع عمومی عادی سالیانه
                "extraordinary", // محمع عمومی فوق العاده
            ])
                ->default("annual");
            $table->unsignedBigInteger("dividend_distribution")
                ->comment("تقسیم سود سالانه")
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
        Schema::dropIfExists('general_meetings');
    }
};
