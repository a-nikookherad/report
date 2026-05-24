<?php

namespace Database\Seeders;

use App\Models\Bourse\Group;
use App\Models\Bourse\Industry;
use App\Models\Bourse\Instrument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MetalIndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // پیدا کردن یا ایجاد شناسه صنعت و گروه
        $industry = Industry::firstOrCreate(['name' => 'فلزات اساسی']);
        $group = Group::firstOrCreate(['name' => 'فلزات اساسی']);

        $instruments = [
            [
                "name" => "فولاد مبارکه اصفهان",
                "slug" => "folad",
                "symbol" => "فولاد",
                "mofid_url" => "symbol=IRO1FOLD0001%3A1&countback=388",
                "financial_period" => "1390-12-29",
                "description" => "شرکت فولاد مبارکه اصفهان که یکی از شرکت های بزرگ بورسی می باشد و تاثیر زیادی در شاخص کل دارد",
            ],
            [
                "name" => "ملی صنایع مس ایران",
                "slug" => "fameli",
                "symbol" => "فملی",
                "mofid_url" => "symbol=IRO1MSMI0001%3A1&countback=588",
                "financial_period" => "1390-12-29",
                "description" => "شرکت ملی صنایع مس ایران",
            ]
        ];

        foreach ($instruments as $item) {
            Instrument::updateOrCreate(
                ['symbol' => $item['symbol']], // شرط برای جلوگیری از تکرار
                [
                    "name" => $item['name'],
                    "slug" => $item['slug'],
                    "mofid_url" => $item['mofid_url'] ?? null,
                    "financial_period" => $item['financial_period'],
                    "group_id" => $group->id,
                    "industry_id" => $industry->id,
                    "description" => $item['description'],
                ]
            );
        }
    }
}
