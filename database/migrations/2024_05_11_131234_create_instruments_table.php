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
        Schema::create('instruments', function (Blueprint $table) {
            $table->id();

            $table->string("name")
                ->nullable();

            $table->string("symbol")
                ->nullable();

            $table->string("slug")
                ->unique();

            $table->string("mofid_url")
                ->nullable();

            $table->string("financial_year")
                ->comment("m-d سال مالی")
                ->nullable();

            $table->string("description")
                ->nullable();

            $table->unsignedBigInteger("industry_id")
                ->nullable()
                ->index();
            $table->foreign("industry_id")
                ->references("id")
                ->on("industries");

            $table->unsignedBigInteger("group_id")
                ->nullable();
            $table->foreign("group_id")
                ->references("id")
                ->on("groups");

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
        Schema::dropIfExists('instruments');
    }
};
