<?php

namespace Database\Seeders;

use App\Models\Workshop;
use Illuminate\Database\Seeder;

class WorkshopSeeder extends Seeder
{
    public function run(): void
    {
        Workshop::factory()->count(5)->create();
        Workshop::factory()->draft()->count(2)->create();
    }
}