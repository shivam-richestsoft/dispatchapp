<?php

namespace Database\Seeders;


use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin')->insert([
            'name'=>'richestsoft',
            'first_name' => 'admin',
            'last_name'=>'richestsoft',
            'email' => 'admin@richestsoft.com',
            'phone_number'=>'1800001291',
            'remember_token' => Str::random(10),
            'password' => Hash::make('password'),
            'created_by_id' => 0
        ]);
    }
}
