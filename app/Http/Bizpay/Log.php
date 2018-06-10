<?php


namespace App\Http\Bizpay;

use App\Http\Models\BizpayLog;

/**
 * Class Log
 * @package App\Http\Bizpay
 */
class Log
{

    /**
     * @param $action
     * @param $response
     * @param $responseTime
     * @param $userId
     * @param $merchantId
     */
    public function insertLog($action, $request, $responseTime,$error, $userId, $merchantId)
    {
        $log = new BizpayLog();
        $log->action = $action;
        $log->request = $request;
        $log->response_time = $responseTime;
        $log->error = $error;
        $log->user_id = $userId;
        $log->merchant_id = $merchantId;

        $log->save();
    }
}