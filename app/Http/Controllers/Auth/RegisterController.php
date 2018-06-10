<?php

namespace App\Http\Controllers\Auth;

use App\Http\Models\Merchant;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $confirmation_code = str_random(30);

        /**
         * User Types:
         *
         * 0: Admin
         * 1: Merchant Admin
         * 2: Merchant Manager
         * 3: Regular User
         *
         */

        $merchant = new Merchant();
        $merchant->merchant_name= $data['merchant-name'];
        $merchant->merchant_id= str_random(10);
        $merchant->status=1;
        $merchant->save();

        $user = new User();
        $user->name=$data['name'];
        $user->email=$data['email'];
        $user->user_id=str_random(20);
        $user->api_token=str_random(30);
        $user->password=bcrypt($data['password']);
        $user->confirmation_code=$confirmation_code;
        $user->user_type=1;
        $user->merchant_id=$merchant->id;
        $user->status=1;
        $user->save();

        return $user;

    }
}
