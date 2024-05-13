<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                "name" => "فلزات اساسی",
                "description" => "گروه فلزات اساسی",
            ],
            [
                "name" => "روانکار",
                "description" => "گروه روانکار",
            ],
            [
                "name" => "کانه های فلزی",
                "description" => "گروه کانه های فلزی",
            ],
        ];

        foreach ($groups as $group) {
            Group::query()
                ->updateOrCreate(["name" => $group["name"]], $group);
        }
    }
}
