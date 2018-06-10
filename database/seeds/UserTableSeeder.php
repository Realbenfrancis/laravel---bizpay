<?php

use Illuminate\Database\Seeder;

/**
 * Class UserTableSeeder
 */
class UserTableSeeder extends Seeder
{
    /**
     *  Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //admin
        \Illuminate\Support\Facades\DB::table('users')->insert([
            'name' => 'Admin',
            'email'    => 'admin@bizpay.co.uk',
            'password' => Hash::make('ahq701nAlpuqZblahxoaQblaAnx917Ajfd'),
            'merchant_id' => 1,
            'status' => 1,
            'user_id' => str_random(20),
            'user_type' => 0,
            'api_limit' => 50000,
            'api_usage' => 50000,
            'api_token' => str_random(30),

        ]);

        //merchant admin
        \Illuminate\Support\Facades\DB::table('users')->insert([
            'name' => 'Merchant Admin',
            'email'    => 'merchant-admin@bizpay.co.uk',
            'password' => Hash::make('Ahwo9631AhpwAbldPqczHfdpYand017AmxlpR'),
            'merchant_id' => 2,
            'status' => 1,
            'user_id' => str_random(20),
            'user_type' => 1,
            'api_limit' => 50000,
            'api_usage' => 50000,
            'api_token' => str_random(30),
        ]);


        //merchant manager
        \Illuminate\Support\Facades\DB::table('users')->insert([
            'name' => 'Merchant Manager',
            'email'    => 'merchant-manager@bizpay.co.uk',
            'password' => Hash::make('Ahwo9631AhpwAbldPqczHfdpYand017AmxlpR'),
            'merchant_id' => 2,
            'status' => 1,
            'user_id' => str_random(20),
            'user_type' => 2,
            'api_token' => str_random(30),
        ]);


        //user
        \Illuminate\Support\Facades\DB::table('users')->insert([
            'name' => 'Merchant Client',
            'email'    => 'user@bizpay.co.uk',
            'password' => Hash::make('Ahwo9631AhpwAbldPqczHfdpYand017AmxlpR'),
            'merchant_id' => 2,
            'status' => 1,
            'user_id' => str_random(20),
            'user_type' => 3,
            'api_token' => str_random(30),
        ]);

    }
}
