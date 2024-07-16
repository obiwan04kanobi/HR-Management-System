<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\LeaveBalanceSeeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(LeaveBalanceSeeder::class);
    }
}
