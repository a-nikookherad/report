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
        Schema::create('capital_increase', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("amount")
                ->default(0);
            $table->enum("type", [
                "through_cash_contribution",// افزایش سرمایه از محل مطالبات و آورده نقدی
                "retained_earnings",// از محل سود انباشته
                "reserves",// از محل اندوخته ها
                "asset_revaluation",// از محل تجدید ارزیابی دارایی ها
                "bonus_share_issue",// افزایش سرمایه به روش صرف سهام
            ]);

            $table->unsignedBigInteger("share_count_before")
                ->comment("تعداد سهام")
                ->nullable();

            $table->unsignedBigInteger("share_count_after")
                ->comment("تعداد سهام بعد از افزایش سرمایه")
                ->nullable();
            $table->enum("status", [
                "pending",
                "complete"
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capital_increase');
    }
};
