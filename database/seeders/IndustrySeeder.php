<?php

namespace Database\Seeders;

use App\Models\Bourse\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            [
                "name" => "استخراج کانه های فلزی",
                "description" => "",
            ],
            [
                "name" => "فرآورده های نفتی، کک و سوخت هسته ای",
                "description" => "",
            ],
            [
                "name" => "فلزات اساسی",
                "description" => "",
            ],
            [
                "name" => "محصولات شیمیایی",
                "description" => "",
            ],
            [
                "name" => "سایر صنایع",
                "description" => "",
            ],
        ];

        foreach ($industries as $industry) {
            Industry::query()
                ->updateOrCreate(["name" => $industry["name"]], $industry);
        }
    }
}
