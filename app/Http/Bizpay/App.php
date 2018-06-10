<?php


namespace App\Http\Bizpay;

use App\Http\Models\Merchant;
use App\Http\Models\SAPlan;
use App\Http\Models\SAPlanPricing;
use App\Http\Models\SAProduct;
use App\Http\Models\SAQuote;
use App\Http\Models\SAQuoteProduct;
use Carbon\Carbon;

/**
 * Class App
 * @package App\Http\Bizpay
 */
class App
{
    /**
     * Create a new product
     *
     * @param $name
     * @param $currency
     * @param $tax
     * @param $userId
     * @param $merchantId
     * @param $productId
     * @param $quantity
     * @param $description
     * @return SAProduct
     */
    public function createProduct(
        $name,
        $currency,
        $tax,
        $price,
        $userId,
        $merchantId,
        $productId,
        $quantity,
        $description,
        $tags
    )
    {

        $product = new SAProduct();
        $product->name = $name;
        $product->slug = str_random(20);
        $product->price = $price;
        $product->product_id = $productId;
        $product->quantity = $quantity;
        $product->description = $description;
        $product->currency = $currency;
        $product->tax = $tax;
        $product->user_id = $userId;
        $product->merchant_id = $merchantId;
        $product->status = 1;
        $product->tags = $tags;
        $product->save();

        return $product;


    }

    public function getProduct($slug, $merchantId)
    {
        $product = SAProduct::GetProduct($slug, $merchantId)[0];

        return $product;
    }

    /**
     * Update product
     *
     * @param $slug
     * @param $name
     * @param $currency
     * @param $tax
     * @param $merchantId
     * @param $productId
     * @param $quantity
     * @param $description
     * @return mixed
     */
    public function updateProduct(
        $slug,
        $price,
        $name,
        $currency,
        $tax,
        $merchantId,
        $productId,
        $quantity,
        $description,
        $tags
    )
    {

        $product = SAProduct::GetProduct($slug, $merchantId)[0];

        $product->name = $name;
        $product->price = $price;
        $product->product_id = $productId;
        $product->quantity = $quantity;
        $product->description = $description;
        $product->currency = $currency;
        $product->tax = $tax;
        $product->tags = $tags;

        $product->save();

        return $product;

    }

    /**
     * Delete product - by slug
     *
     * @param $slug
     * @param $merchantId
     */
    public function deleteProduct($slug, $merchantId)
    {
        $product = SAProduct::GetProduct($slug, $merchantId)[0];
        SAPlan::destroy($product->id);

        return true;
    }

    /**
     * List all products for a mechant
     *
     * @param $merchantId
     * @return mixed
     */
    public function allProducts($merchantId)
    {

        $products = SAProduct::GetAllProductForMerchant($merchantId);


        return $products;

    }

    /**
     * Create a new plan
     *
     * @param $planName
     * @param $structure
     * @param $billingStart
     * @param $billingPeriod
     * @param $paymentInfoRequired
     * @param $differentFirstPayment
     * @param $firstPayment
     * @param $firstPaymentDate
     * @param $refundPercent
     * @param $renewal
     * @param $agreementTerm
     * @param $canCancel
     * @param $cancellationDays
     * @param $refundCheck
     * @param $terms
     * @param $userId
     * @param $instalments
     * @param $merchantId
     * @return SAPlan
     */
    public function createPlan(
        $planName,
        $structure,
        $billingStart,
        $billingPeriod,
        $paymentInfoRequired,
        $differentFirstPayment,
        $firstPayment,
        $firstPaymentDate,
        $refundPercent,
        $renewal,
        $agreementTerm,
        $canCancel,
        $cancellationDays,
        $refundCheck,
        $terms,
        $userId,
        $instalments,
        $merchantId
    )
    {


        $plan = new SAPlan();
        $plan->plan = $planName;
        $plan->slug = str_random(20);
        $plan->structure = $structure;
        $plan->billing_start = $billingStart;
        $plan->billing_period = $billingPeriod;
        $plan->payment_info_required = $paymentInfoRequired;
        $plan->different_first_payment = $differentFirstPayment;
        $plan->first_payment = $firstPayment;
        $plan->first_payment_date = $firstPaymentDate;
        $plan->can_cancel = $canCancel;
        $plan->cancellation_days = $cancellationDays;
        $plan->refund_check = $refundCheck;
        $plan->refund_percent = $refundPercent;
        $plan->renewal = $renewal;
        $plan->agreement_term = $agreementTerm;
        $plan->terms = $terms;
        $plan->user_id = $userId;
        $plan->merchant_id = $merchantId;
        $plan->status = 1;
        $plan->save();


        foreach ($instalments as $instalment) {
            $instalment = ((array)$instalment);
            $planDuration = new SAPlanPricing();
            $planDuration->instalments = $instalment["duration"];
            $planDuration->price_change = $instalment["change"];
            $planDuration->default_check = $instalment["default"];
            $planDuration->s_a_plan_id = $plan->id;
            $planDuration->merchant_id = $merchantId;
            $planDuration->save();
        }

        return $plan;


    }

    /**
     * Get plan details
     *
     * @param $slug
     * @param $merchantId
     * @return mixed
     */
    public function getPlan($slug, $merchantId)
    {
        $plan = SAPlan::GetPlan($slug, $merchantId);
        if (count($plan) > 0) {
            return $plan[0];
        } else {
            return false;
        }
    }

    /**
     * Update Plan
     *
     * @param $planName
     * @param $structure
     * @param $slug
     * @param $billingStart
     * @param $billingPeriod
     * @param $paymentInfoRequired
     * @param $differentFirstPayment
     * @param $firstPayment
     * @param $firstPaymentDate
     * @param $refundPercent
     * @param $renewal
     * @param $agreementTerm
     * @param $canCancel
     * @param $cancellationDays
     * @param $refundCheck
     * @param $terms
     * @param $instalments
     * @param $merchantId
     * @return mixed
     */
    public function updatePlan(
        $planName,
        $structure,
        $slug,
        $billingStart,
        $billingPeriod,
        $paymentInfoRequired,
        $differentFirstPayment,
        $firstPayment,
        $firstPaymentDate,
        $refundPercent,
        $renewal,
        $agreementTerm,
        $canCancel,
        $cancellationDays,
        $refundCheck,
        $terms,
        $instalments,
        $merchantId
    )
    {

        $plan = SAPlan::GetBySlug($slug)[0];
        $plan->plan = $planName;
        $plan->structure = $structure;
        $plan->billing_start = $billingStart;
        $plan->billing_period = $billingPeriod;
        $plan->payment_info_required = $paymentInfoRequired;
        $plan->different_first_payment = $differentFirstPayment;
        $plan->first_payment = $firstPayment;
        $plan->first_payment_date = $firstPaymentDate;
        $plan->can_cancel = $canCancel;
        $plan->cancellation_days = $cancellationDays;
        $plan->refund_check = $refundCheck;
        $plan->refund_percent = $refundPercent;
        $plan->renewal = $renewal;
        $plan->agreement_term = $agreementTerm;
        $plan->terms = $terms;
        $plan->merchant_id = $merchantId;
        $plan->save();

        $pricings = $plan->pricing->all();

        foreach ($pricings as $pricing) {
            SAPlanPricing::destroy($pricing->id);
        }

        foreach ($instalments as $instalment) {

            $instalment = ((array)$instalment);
            $planDuration = new SAPlanPricing();
            $planDuration->instalments = $instalment['duration'];
            $planDuration->price_change = $instalment['change'];
            $planDuration->default_check = $instalment['default'];
            $planDuration->s_a_plan_id = $plan->id;
            $planDuration->merchant_id = $merchantId;
            $planDuration->save();
        }

        return $plan;

    }

    /**
     * Delete a plan - by slug
     *
     * @param $slug
     * @param $merchantId
     */
    public function deletePlan(
        $slug,
        $merchantId
    )
    {
        $plan = SAPlan::GetPlan($slug, $merchantId)[0];

        $pricings = $plan->pricing->all();

        foreach ($pricings as $pricing) {
            SAPlanPricing::destroy($pricing->id);
        }

        SAPlan::destroy($plan->id);

        return true;
    }

    /**
     * List all plans for merchant
     *
     * @param $merchantId
     * @return mixed
     */
    public function allPlans($merchantId)
    {
        $plans = SAPlan::GetAllPlansByMerchant($merchantId);

        return $plans;

    }

    /**
     * Create new quote
     *
     * @param $name
     * @param $planId
     * @param $merchantId
     * @param $validity
     * @param $confirmationUrl
     * @param $prePopulateCheck
     * @param $userId
     * @param $purchaseLimit
     * @param $validityType
     * @return SAQuote
     */
    public function createQuote(
        $name,
        $planId,
        $merchantId,
        $validity,
        $confirmationUrl,
        $userId,
        $validityType,
        $products,
        $customerId,
        $purchaseLimit=null
    )
    {
        $plan = SAPlan::GetBySlug($planId)[0];
        $quote = new SAQuote();
        $quote->slug = str_random(20);
        $quote->name = $name;
        $quote->purchase_limit = $purchaseLimit;
        $quote->validity_type = $validityType;

        switch ($validityType){
            case 0:
                $vDate=0;
                break;
            case 1:
                $vDate = Carbon::parse($validity)->format("d/m/Y");
                break;
            case 2:
                $now = Carbon::now();
                if($validity>1){
                    $vDate = $now->addDays($validity)->format("d/m/Y");
                } else {
                    $vDate = $now->addDay($validity)->format("d/m/Y");
                }
                break;
            case 3:
                $vDate=0;
        }

        $quote->validity=$vDate;




        $quote->validity = $validity;
        $quote->confirmation_url = $confirmationUrl;
        $quote->plan_id = $plan->id;
        $quote->client_id = $customerId;
        $quote->user_id = $userId;
        $quote->merchant_id = $merchantId;
        $quote->status = 1;
        $quote->save();


        foreach ($products as $product) {

            $quoteProduct = new SAQuoteProduct();
            $product = ((array)$product);
            $productObject = SAProduct::GetBySlug($product['id'])[0];
            $quoteProduct->s_a_product_id = $productObject->id;
            $quoteProduct->quantity = $product['quantity'];
            $quoteProduct->s_a_quote_id = $quote->id;
            $quoteProduct->save();
        }

        return $quote;
    }

    /**
     * @param $slug
     * @param $merchantId
     * @return mixed
     */
    public function getQuote($slug, $merchantId)
    {
        $quote = SAQuote::GetQuoteByMerchant($slug, $merchantId);

        if (count($quote) > 0) {
            return $quote[0];
        } else {
            return false;
        }

    }

    /**
     * Update a quote
     *
     * @param $name
     * @param $slug
     * @param $planId
     * @param $merchantId
     * @param $validity
     * @param $confirmationUrl
     * @param $prePopulateCheck
     * @param $userId
     * @param $purchaseLimit
     * @param $validityType
     * @return mixed
     */
    public function updateQuote(
        $name,
        $planId,
        $slug,
        $merchantId,
        $validity,
        $confirmationUrl,
        $validityType,
        $products,
        $customerId,
        $purchaseLimit=null
    )
    {
        $quote = SAQuote::GetQuoteByMerchant($slug, $merchantId);


        if (count($quote) > 0) {

            $plan = SAPlan::GetBySlug($planId)[0];
            $quote = $quote[0];
            $quote->name = $name;
            $quote->purchase_limit = $purchaseLimit;


            switch ($validityType){
                case 0:
                    $vDate=0;
                    break;
                case 1:
                    $vDate = Carbon::createFromFormat("d/m/Y",$validity)->format("d/m/Y");
                    break;
                case 2:
                    $now = Carbon::now();
                    if($validity>1){
                        $vDate = $now->addDays($validity)->format("d/m/Y");
                    } else {
                        $vDate = $now->addDay($validity)->format("d/m/Y");
                    }
                    break;
                case 3:
                    $vDate=0;
            }

            $quote->validity=$vDate;

            $quote->confirmation_url = $confirmationUrl;
            $quote->plan_id = $plan->id;
            $quote->client_id = $customerId;
            $quote->save();

            $currentProducts = $quote->products->all();

            foreach ($currentProducts as $product) {
                SAQuoteProduct::destroy($product->id);
            }

            foreach ($products as $product) {
                $quoteProduct = new SAQuoteProduct();
                $product = ((array)$product);
                $productObject = SAProduct::GetBySlug($product['id'])[0];
                $quoteProduct->s_a_product_id = $productObject->id;
                $quoteProduct->quantity = $product['quantity'];
                $quoteProduct->s_a_quote_id = $quote->id;
                $quoteProduct->save();
            }

            return true;
        } else {
            return false;
        }


    }

    /**
     * Delete a quote - by slug
     *
     * @param $slug
     * @param $merchantId
     * @return bool
     */
    public function deleteQuote($slug, $merchantId)
    {
        $quote = SAQuote::GetQuoteByMerchant($slug, $merchantId);

        if (count($quote) > 0) {
            $quote = $quote[0];
            $currentProducts = $quote->products->all();

            foreach ($currentProducts as $product) {
                SAQuoteProduct::destroy($product->id);
            }

            SAPlan::destroy($quote->id);
            return true;
        } else {
            return false;
        }


    }

    /**
     * List all quotes by merchant
     *
     * @param $merchantId
     * @return mixed
     */
    public function allQuotes($merchantId)
    {
        $quotes = SAQuote::GetAllQuotesByMerchant($merchantId);

        return $quotes;
    }

    /**
     * Save merchant settings
     *
     * @param $merchantId
     * @param $merchantWebsite
     * @param $merchantLogo
     * @param $merchantGateway
     * @param $merchantName
     * @param $merchantPhoneNumber
     * @param $merchantCompanyNo
     * @param $merchantTaxNo
     * @param $merchantStaffNumber
     * @param $merchantIndustry
     */
    public function saveSettings(
        $merchantId,
        $merchantWebsite,
        $merchantLogo,
        $merchantGateway,
        $merchantName,
        $merchantPhoneNumber,
        $merchantCompanyNo,
        $merchantTaxNo,
        $merchantStaffNumber,
        $merchantIndustry
    )
    {

        $merchant = Merchant::findorFail($merchantId);
        $merchant->number_of_staff = $merchantStaffNumber;
        $merchant->industry = $merchantIndustry;
        $merchant->organisation_number = $merchantCompanyNo;
        $merchant->tax_number = $merchantTaxNo;
        $merchant->merchant_name = $merchantName;
        $merchant->phone_number = $merchantPhoneNumber;
        $merchant->merchant_website = $merchantWebsite;
        $merchant->merchant_logo = $merchantLogo;
        $merchant->gateway = $merchantGateway;
        $merchant->save();
    }


}