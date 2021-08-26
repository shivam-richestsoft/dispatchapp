<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Management;
class ManagementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Management::factory()->count(100)->create();
    }
}
