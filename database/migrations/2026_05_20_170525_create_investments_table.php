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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("investor_id");
            $table->foreign("investor_id")
                ->references("id")
                ->on("instruments");

            $table->unsignedBigInteger("investment_target_id");
            $table->foreign("investment_target_id")
                ->references("id")
                ->on("instruments");

            $table->unsignedSmallInteger("percent");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
