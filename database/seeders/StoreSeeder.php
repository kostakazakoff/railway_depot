<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        Store::create(['name' => '']);
        Store::create(['name' => 'T345']);
        Store::create(['name' => 'T5789']);
        Store::create(['name' => 'T9875']);
        Store::create(['name' => 'T9876']);
        Store::create(['name' => 'T098']);
        Store::create(['name' => 'T349']);
    }
}

/*
sail a db:seed --class=StoreSeeder
*/
