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
        Schema::create('ipo', function (Blueprint $table) {
            $table->id();
            $table->string("symbol")
                ->nullable();
            $table->unsignedBigInteger("price")
                ->nullable();
            $table->unsignedBigInteger("quantity")
                ->nullable();
            $table->boolean("success")
                ->nullable();
            $table->integer("status")
                ->nullable();
            $table->text("body")
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipo');
    }
};
