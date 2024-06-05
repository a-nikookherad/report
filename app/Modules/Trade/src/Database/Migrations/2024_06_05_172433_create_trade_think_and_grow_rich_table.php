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
        Schema::create('trade_think_and_grow_rich', function (Blueprint $table) {
            $table->id();
            $table->float("how_many_you_want")
                ->comment("first step")
                ->nullable();

            $table->string("cost_of_your_request")
                ->comment("second step")
                ->nullable();

            $table->dateTime("date_for_request")
                ->comment("third step")
                ->nullable();

            $table->text("plan_for_request")
                ->comment("fifth step")
                ->nullable();

            $table->text("conclude_every_fifth_steps")
                ->comment("sixth step(read it every morning and every knight)")
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_think_and_grow_rich');
    }
};
