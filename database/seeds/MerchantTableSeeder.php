<?php

use Illuminate\Database\Seeder;

class MerchantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = \Faker\Factory::create();

//        \Illuminate\Support\Facades\DB::table('merchants')->insert([
//            'merchant_id' => str_random(16),
//            'merchant_name' => $faker->name,
//            'merchant_website' => $faker->url,
//            'merchant_stripe_public_key' => $faker->slug(10),
//            'merchant_stripe_secret_key' => $faker->slug(10),
//            'status' => 1,
//
//        ]);


        //\App\Http\Models\Merchant::truncate();

        \App\Http\Models\Merchant::create([
            'merchant_id' => str_random(16),
            'merchant_name' => "Bizpay",
            'merchant_website' => "bizpay.co.uk",
//                'merchant_stripe_public_key' => "pk_test_WwWPAmfzhrZpWdCd0fHR468G",
//                'merchant_stripe_secret_key' => "sk_test_YdyShyG1Y8oAnt46VL2b81nT",
            'status' => 1,
        ]);


        for ($i = 0; $i < 5; $i++) {
            \App\Http\Models\Merchant::create([
                'merchant_id' => str_random(16),
                'merchant_name' => $faker->name,
                'merchant_website' => $faker->url,
//                'merchant_stripe_public_key' => "pk_test_WwWPAmfzhrZpWdCd0fHR468G",
//                'merchant_stripe_secret_key' => "sk_test_YdyShyG1Y8oAnt46VL2b81nT",
                'status' => 1,
            ]);
        }
    }
}
