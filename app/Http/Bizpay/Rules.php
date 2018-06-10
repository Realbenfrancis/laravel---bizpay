<?php

namespace App\Http\Bizpay;

use App\Http\Models\Merchant;
use App\Http\Models\Rule;
use Illuminate\Support\Facades\Auth;

/**
 * Class Rules
 * Bizpay Rules Engine
 *
 * @package App\Http\Bizpay
 */
class Rules
{

    private $user;
    private $merchantId;

    /**
     * Rules constructor.
     * @param $merchantId
     */
    public function __construct($merchantId)
    {
        $this->merchantId = $merchantId;
    }


    public function initialPayment()
    {
    }

    /**
     * Get allowed plan durations
     *
     * @return array
     */
    public function planMonths()
    {
        $merchant = Merchant::findorFail($this->merchantId);
        $rules = $merchant->rules->all();
        $months = array();

        foreach ($rules as $rule) {
            if ($rule->apply_rule_on == "plan"
                && $rule->check_type == "equal"
                && $rule->data_type == "Integer"
                && $rule->action_on == "credit"
                && $rule->action_type == "Boolean"
                && mb_strtolower($rule->action_value) == "true"
            ) {
                array_push($months, $rule->limit1);
            }
        }

        return $months;
    }

    /**
     * Get disallowed countries
     *
     * @return array
     */
    public function clientDisallowedCountries()
    {
        $countries = Rule::DisAllowedCountriesCheck();
        $countriesList = array();

        foreach ($countries as $item) {
            array_push($countriesList, $item['limit1']);
        }

        return $countriesList;
    }


    /**
     * Get disallowed countries by Admin
     *
     * @return array
     */
    public function clientDisallowedCountriesByAdmin()
    {
        $countries = Rule::DisAllowedCountriesCheckByAdmin($this->merchantId);
        $countriesList = array();

        foreach ($countries as $item) {
            array_push($countriesList, $item['limit1']);
        }

        return $countriesList;
    }

    public function priceIncrease()
    {
    }

    /**
     * Check the minimum
     * @return mixed
     */
    public function clientMinimumAge()
    {
        if(count(Rule::ClientAgeCheck())>0){
            return Rule::ClientAgeCheck()[0]['limit1'];
        } else {
            return false;
        }

    }

    /**
     * @return mixed
     */
    public function clientMinimumAgeSetByAdmin()
    {
        return Rule::ClientAgeCheckByAdmin($this->merchantId)[0]['limit1'];
    }

    /**
     * @return mixed
     */
    public function trialPeriod()
    {
        return Rule::TrialPeriod()[0]['limit1'];
    }

    /**
     * @return mixed
     */
    public function minimumPriceForPlan()
    {

        if(count(Rule::ClientAgeCheck())>0){
            return Rule::MinumumPriceForCredit()[0]['limit1'];
        } else {
            return false;
        }

    }

    /**
     * @return mixed
     */
    public function minimumPriceForPlanByAdmin()
    {
        return Rule::MinumumPriceForCreditByAdmin()[0]['limit1'];
    }


    public function creditAvailableCheck()
    {
    }

    /**
     * @param $name
     * @param $checkType
     * @param $applyRuleOn
     * @param $description
     * @param $dataType
     * @param $limit1
     * @param $limit2
     * @param $limit3
     * @param $actionOn
     * @param $actionType
     * @param $actionValue
     * @param $merchantIdon
     * @param $userId
     */
    public function addRule(
        $name,
        $checkType,
        $applyRuleOn,
        $description,
        $dataType,
        $limit1,
        $limit2,
        $limit3,
        $actionOn,
        $actionType,
        $actionValue,
        $merchantIdon,
        $userId
    ) {
        $rule = new Rule();
        $rule->rule = $name;
        $rule->check_type = $checkType;
        $rule->apply_rule_on = $applyRuleOn;
        $rule->description = $description;
        $rule->data_type = $dataType;
        $rule->limit1 = $limit1;
        $rule->limit2 = $limit2;
        $rule->limit3 = $limit3;
        $rule->action_on = $actionOn;
        $rule->action_type = mb_strtolower($actionType);
        $rule->action_value = $actionValue;
        $rule->merchant_id = $this->merchantId;
        if ($merchantIdon > 0) {
            $rule->merchant_id_on = $merchantIdon;
        } else {
            $rule->merchant_id_on = null;
        }

        $rule->user_id = $userId;
        $rule->rule_id = str_random(20);
        $rule->status = 1;
        $rule->save();
    }
}