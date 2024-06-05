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
        Schema::create('trade_strategies', function (Blueprint $table) {
            $table->id();
            $table->string("name")
                ->nullable();

            $table->string("conditions_for_enter")
                ->comment("where and why enter")
                ->nullable();

            $table->string("where_exit_if_win")
                ->nullable();

            $table->string("where_exit_if_loss")
                ->comment("when my analyse is wrong")
                ->nullable();

            $table->string("type_of_strategy")
                ->comment("trend or range...")
                ->nullable();

            $table->string("technicals")
                ->nullable();

            $table->text("description")
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_strategies');
    }
};
