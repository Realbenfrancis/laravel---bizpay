<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MerchantTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(OrderTableSeeder::class);
        $this->call(PaymentTableSeeder::class);
        $this->call(RulesTableSeeder::class);
        $this->call(PaymentGatewayTableSeeder::class);

    }
}
