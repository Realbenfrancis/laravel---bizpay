<?php

namespace App\Console\Commands;

use App\Http\Bizpay\Bizpay;
use App\Http\Bizpay\SendGrid;
use App\Http\Models\DeferredCharge;
use App\Http\Models\Merchant;
use App\Http\Models\SAAgreement;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Class ChargeCustomer
 * @package App\Console\Commands
 */
class ChargeCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charge:deferred';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to process deferred charges';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * Find the charges due for the day and call the relevant payment gateway to process the charge
     *
     * @return mixed
     *
     */
    public function handle()
    {
        $deferredCharges = DeferredCharge::PaymentsDueToday();


        $bizpay = new Bizpay();

        foreach ($deferredCharges as $deferredCharge) {

            $testCheck = env('API_ENV');

            if ($testCheck == "test") {
                $check = ($deferredCharge->status == 1 && $deferredCharge->test_check == 1);
            } else {
                $check = ($deferredCharge->status == 1);
            }

            if ($check) {

                $user = User::findorFail($deferredCharge->user_id);

                if ($deferredCharge->payment_gateway == 1) {
                    $bizpay->setStripeCredential($user->merchant_id);
                }

                if ($deferredCharge->payment_gateway == 2) {
                    $bizpay->setGoCardlessCredential($user->merchant_id);
                }



                try {

                    $resp = $bizpay->chargeClient(
                        $user,
                        $deferredCharge->amount,
                        $deferredCharge->currency_code,
                        $deferredCharge->description,
                        $deferredCharge->payment_gateway,
                        $deferredCharge->tax,
                        "dc-" . $deferredCharge->id,
                        $deferredCharge->order_ref

                    );



                } catch (\Exception $e) {

                   // dd($e);
                }


                try {
                    if (strlen($resp) > 1) {
                        if ($deferredCharge->order_type == 2) {
                            $duration = $deferredCharge->duration;

                            $formattedFirstDate = Carbon::parse($deferredCharge->payment_date);

                            if ($duration == "day") {
                                $formattedFirstDate->addDay();
                            }

                            if ($duration == "2-days") {
                                $formattedFirstDate->addDays(2);
                            }

                            if ($duration == "3-days") {
                                $formattedFirstDate->addDays(3);
                            }

                            if ($duration == "4-days") {
                                $formattedFirstDate->addDays(4);
                            }

                            if ($duration == "5-days") {
                                $formattedFirstDate->addDays(5);
                            }

                            if ($duration == "6-days") {
                                $formattedFirstDate->addDays(6);
                            }

                            if ($duration == "week") {
                                $formattedFirstDate->addWeek();
                            }

                            if ($duration == "2-weeks") {
                                $formattedFirstDate->addWeeks(2);
                            }

                            if ($duration == "3-weeks") {
                                $formattedFirstDate->addWeeks(3);
                            }

                            if ($duration == "month") {
                                $formattedFirstDate->addMonth();
                            }

                            if ($duration == "2-months") {
                                $formattedFirstDate->addMonths(2);
                            }

                            if ($duration == "3-months") {
                                $formattedFirstDate->addMonths(3);
                            }

                            if ($duration == "4-months") {
                                $formattedFirstDate->addMonths(4);
                            }

                            if ($duration == "5-months") {
                                $formattedFirstDate->addMonths(5);
                            }

                            if ($duration == "6-months") {
                                $formattedFirstDate->addMonths(6);
                            }

                            if ($duration == "year") {
                                $formattedFirstDate->addYear();
                            }

                            if ($duration == "2-years") {
                                $formattedFirstDate->addYears(2);
                            }

                            if ($duration == "3-years") {
                                $formattedFirstDate->addYears(3);
                            }

                            if ($duration == "4-years") {
                                $formattedFirstDate->addYears(4);
                            }

                            if ($duration == "5-years") {
                                $formattedFirstDate->addYears(5);
                            }

                            if ($duration == "10-years") {
                                $formattedFirstDate->addYears(10);
                            }

                            if ($duration == "15-years") {
                                $formattedFirstDate->addYears(15);
                            }

                            if ($duration == "20-years") {
                                $formattedFirstDate->addYears(20);
                            }

                            if ($duration == "25-years") {
                                $formattedFirstDate->addYears(25);
                            }

                            $deferredCharge->payment_date = $formattedFirstDate->toDateString();
                            if ($deferredCharge->instalments_remaining > 0) {
                                $deferredCharge->instalments_remaining = ($deferredCharge->instalments_remaining) - 1;
                            }

                            if ($deferredCharge->instalments_remaining == 0) {
                                $deferredCharge->status = 0;
                            }

                            $deferredCharge->save();

                        } else {

                            $deferredCharge->instalments_remaining = 0;
                            $deferredCharge->status = 0;

                            $passive = true;
                            $dPayments = DeferredCharge::PaymentsByRef($deferredCharge->order_ref, $deferredCharge->merchant_id);

                            foreach ($dPayments as $dPayment) {
                                if ($dPayment->status == 1) {
                                    $passive = false;
                                }
                            }

                            if ($passive) {

                                $agreement = SAAgreement::GetBySlug($deferredCharge->order_ref)[0];
                                $agreement->status = 0;
                                $agreement->save();
                            }

                            $deferredCharge->save();
                        }
                    } else {
                        $agreement = SAAgreement::GetBySlug($deferredCharge->order_ref)[0];
                        $agreement->status = -2;
                        $agreement->save();
                        $deferredCharge->status = -1;
                        $deferredCharge->save();
                        $sendGrid = new SendGrid();
                        $merchantAdmin = User::GetMerchantAdmin($agreement->merchant_id)[0];
                        $merchant = Merchant::findorFail(($agreement->merchant_id));

                        $sendGrid->paymentMissed($merchant->merchant_logo, $merchant->merchant_website, $user->name, $user->email, $agreement->merchant_slug, $merchant->merchant_name, $merchantAdmin->email, $deferredCharge->amount, $deferredCharge->currency_code, $deferredCharge->payment_date);
                    }


                } catch (\Exception $e) {


                    //  send notification : post-mvp
                }

            }
        }
    }
}
