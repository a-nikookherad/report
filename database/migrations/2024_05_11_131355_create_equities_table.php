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
        Schema::create('equities', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("capital")
                ->comment("سرمایه");

            $table->bigInteger("accumulated_profits")
                ->comment("سود انباشته")
                ->default(0);

            $table->enum("order", [
                1,
                2,
                3,
                4
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

            $table->json("script")
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equities');
    }
};
