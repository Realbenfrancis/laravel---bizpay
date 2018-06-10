<?php


namespace App\Http\Bizpay;

use App\Http\Models\Merchant;
use App\User;

/**
 * Manage everything associated with sending data to Bizpay core API
 *
 * Class Api
 * @package App\Http\Bizpay
 */
class Api
{
    private $user;
    private $response;

    /**
     * Api constructor.
     * @param $api
     */
    public function __construct($api)
    {
        if(strlen($api)>1){
            $this->user = User::GetUserFromAPIKey($api);




            $this->response = array();

            if (count($this->user) < 1) {
              //  exit("Invalid key");

                ex it(response()->json([
                    "error" => "Invalid key"
                ], 401));

            } else {
                $this->user = $this->user[0];
                $this->user->api_usage = $this->user->api_usage +1;
                $this->user->save();

                if($this->user->api_usage>$this->user->api_limit){
                    exit(response()->json([
                        "error" => "API usage exceeded"
                    ], 429));
                }

            }
        } else{
            exit(response()->json([
                "error" => "Invalid key"
            ], 401));
        }

    }

    /**
     * Returns the current user object (i.e. who is using the API)
     *
     * @return mixed
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * Returns the merchant object associated with the user
     *
     * @return mixed
     */
    public function merchant()
    {
        $merchant = Merchant::findorFail($this->user->merchant_id);
        return $merchant;
    }

    /**
     * ONLY FOR TESTING
     * Checks this the user can access the client object
     *
     * @param $email
     * @return mixed
     */
    public function getUserForMerchant($email)
    {
        $user = User::GetUserFromEmail($email);
        if (count($user) < 1) {
            exit('Invalid Request');
        } else {
            return $user[0];
        }
    }


    /**
     * @param $userId
     * @return mixed
     */
    public function GetUserFromUserIdForMerchant($userId,$merchantId)
    {
        $user = User::GetUserForMerchantById($userId,$merchantId);

        if (count($user) < 1) {
            exit('Invalid Request');
        } else {
            return $user[0];
        }
    }

    /**
     * @param $email
     * @param $merchantId
     * @return null
     */
    public function checkUserExistsForMerchant($email,$merchantId)
    {
        $user = User::CheckUserExistsForMerchant($email,$merchantId);
        if (count($user) < 1) {
            return null;
        } else {
            return $user[0];
        }
    }


    /**
     * TESTING ONLY
     *
     * @param $email
     * @return null
     */
    public function checkUserForMerchant($email)
    {
        $user = User::GetUserFromEmail($email);
        if (count($user) < 1) {
            return null;
        } else {
            return $user[0];
        }
    }



    /**
     * Checks this the user can access the client object
     *
     * @param $userId
     * @return mixed
     */
    public function getUserByUserIDForMerchant($userId)
    {
        $user = User::GetUserFromUserId($userId);
        if (count($user) < 1) {
            exit('Invalid Request');
        } else {
            return $user[0];
        }
    }

}
