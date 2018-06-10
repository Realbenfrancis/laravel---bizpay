<?php

namespace App\Http\Bizpay;

use App\User;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserManagement
 * @package App\Http\Bizpay
 */
class UserManagement
{

    /**
     * @return UserManagement|null
     */
    public static function instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new self();
        }
        return $inst;
    }

    /**
     *
     * Add a new user (customer)
     *
     * @param $merchantId
     * @param $name
     * @param $email
     * @param $password
     * @param null $addressLine1
     * @param null $addressLine2
     * @param null $city
     * @param null $postCode
     * @param null $country
     * @param null $organisationName
     * @param null $companyNumber
     * @param null $role
     * @param null $phoneNumber
     * @param null $jobTitle
     * @return User
     */
    public function addUser(
        $merchantId,
        $name,
        $email,
        $password,
        $firstName = null,
        $lastName = null,
        $addressLine1 = null,
        $addressLine2 = null,
        $city = null,
        $postCode = null,
        $country = null,
        $organisationName = null,
        $companyNumber = null,
        $role = null,
        $phoneNumber = null,
        $jobTitle = null
    )
    {
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->merchant_id = $merchantId;
        $user->user_id = str_random(20);
        $user->password = $password;

        $user->first_name = $firstName;
        $user->last_name = $lastName;

        $user->address_line1 = $addressLine1;
        $user->address_line2 = $addressLine2;
        $user->city = $city;
        $user->postcode = $postCode;
        $user->country = $country;
        $user->organisation_name = $organisationName;
        $user->company_no = $companyNumber;
        $user->role = $role;
        $user->phone_number = $phoneNumber;
        $user->job_title = $jobTitle;


        $user->user_type = 3;
        $user->status = 1;
        $user->api_token = str_random(30);
        $user->confirmation_code = str_random(6);
        $user->save();

        return $user;
    }

    /**
     * Add a manager user
     *
     * @param $merchantId
     * @param $name
     * @param $email
     * @param $password
     * @return User
     */
    public function addManager($merchantId, $name, $email, $password)
    {
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->merchant_id = $merchantId;
        $user->user_id = str_random(20);
        $user->password = $password;
        $user->user_type = 2;
        $user->status = 1;
        $user->confirmation_code = str_random(6);
        $user->save();

        return $user;
    }

}