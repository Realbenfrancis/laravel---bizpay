<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Rule
 * @package App\Http\Models
 */
class Rule extends Model
{
    /**
     * Get the rule from rule id
     *
     * @param $query
     * @param $slug
     * @return mixed
     */
    public function scopeGetRuleFromSlug($query, $slug)
    {
        return $query->where('rule_id', '=', $slug)->get();
    }

    /**
     *
     * @param $query
     * @return mixed
     */
    public function scopeClientAgeCheck($query)
    {
        return $query->select('limit1')
            ->where('apply_rule_on', '=', "client_age")
            ->where('check_type', '=', "greater")
            ->where('data_type', '=', "Integer")
            ->where('action_on', '=', "credit")
            ->where('action_type', '=', "Boolean")
            ->where('action_value', '=', "true")
            ->get();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeClientAgeCheckByAdmin($query,$merchantId)
    {
        return $query->select('limit1')
            ->where('apply_rule_on', '=', "client_age")
            ->where('check_type', '=', "greater")
            ->where('data_type', '=', "Integer")
            ->where('action_on', '=', "credit")
            ->where('action_type', '=', "Boolean")
            ->where('action_value', '=', "true")
            ->where('merchant_id_on', '=', $merchantId)
            ->get();
    }


    /**
     * @param $query
     * @return mixed
     */
    public function scopeDisAllowedCountriesCheck($query)
    {
        return $query->select('limit1')
            ->where('apply_rule_on', '=', "client_country")
            ->where('check_type', '=', "equal")
            ->where('data_type', '=', "String")
            ->where('action_on', '=', "credit")
            ->where('action_type', '=', "Boolean")
            ->where('action_value', '=', "false")
            ->get();
    }

    /**
     * @param $query
     * @param $merchantId
     * @return mixed
     */
    public function scopeDisAllowedCountriesCheckByAdmin($query,$merchantId)
    {
        return $query->select('limit1')
            ->where('apply_rule_on', '=', "client_country")
            ->where('check_type', '=', "equal")
            ->where('data_type', '=', "String")
            ->where('action_on', '=', "credit")
            ->where('action_type', '=', "Boolean")
            ->where('action_value', '=', "false")
            ->where('merchant_id_on', '=', $merchantId)
            ->get();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeTrialPeriod($query)
    {
        return $query->select('limit1')
            ->where('apply_rule_on', '=', "offer_trial_for")
            ->where('action_on', '=', "credit")
            ->where('action_type', '=', "Boolean")
            ->where('action_value', '=', "true")
            ->get();
    }

    public function scopeMinumumPriceForCredit($query)
    {
        return $query->select('limit1')
            ->where('apply_rule_on', '=', "offer_plan_only")
            ->where('check_type', '=', "greater")
            ->where('data_type', '=', "Integer")
            ->where('action_on', '=', "credit")
            ->where('action_type', '=', "Boolean")
            ->where('action_value', '=', "true")
            ->get();
    }


    public function scopeMinumumPriceForCreditByAdmin($query,$merchantId)
    {
        return $query->select('limit1')
            ->where('apply_rule_on', '=', "offer_plan_only")
            ->where('check_type', '=', "greater")
            ->where('data_type', '=', "Integer")
            ->where('action_on', '=', "credit")
            ->where('action_type', '=', "Boolean")
            ->where('action_value', '=', "true")
            ->where('merchant_id_on', '=', $merchantId)
            ->get();
    }
}
