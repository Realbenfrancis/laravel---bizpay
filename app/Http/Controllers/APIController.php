<?php

namespace App\Http\Controllers;

use App\Http\Bizpay\Api;
use App\Http\Bizpay\App;
use App\Http\Bizpay\Bizpay;
use App\Http\Bizpay\Log;
use App\Http\Bizpay\Rules;
use App\Http\Bizpay\SendGrid;
use App\Http\Models\DeferredCharge;
use App\Http\Models\Merchant;
use App\Http\Models\Order;
use App\Http\Models\SAAgreement;
use App\Http\Models\SAPlan;
use App\User;
use Carbon\Carbon;
use GoCardlessPro\Client;

/**
 * Class APIController
 * @package App\Http\Controllers
 */
class APIController extends Controller
{
    // use AuthenticatesUsers;

    private $user;
    private $bizpay;
    private $api;
    private $app;
    private $log;
    private $mail;
    private $countries;
    private $countriesBlocked; // Rules may be applied on top of this
    private $currencies;

    /**
     * APIController constructor.
     */
    public function __construct()
    {
        $key = request()->headers->get('x-bizpay-key');
        $this->api = new Api($key);
        $this->user = $this->api->user();
        $this->bizpay = new Bizpay();
        $this->app = new App();
        $this->log = new Log();
        $this->mail = new SendGrid();

        $this->currencies = array(
            'AFA' => array('Afghan Afghani', '971'),
            'AWG' => array('Aruban Florin', '533'),
            'AUD' => array('Australian Dollars', '036'),
            'ARS' => array('Argentine Pes', '032'),
            'AZN' => array('Azerbaijanian Manat', '944'),
            'BSD' => array('Bahamian Dollar', '044'),
            'BDT' => array('Bangladeshi Taka', '050'),
            'BBD' => array('Barbados Dollar', '052'),
            'BYR' => array('Belarussian Rouble', '974'),
            'BOB' => array('Bolivian Boliviano', '068'),
            'BRL' => array('Brazilian Real', '986'),
            'GBP' => array('British Pounds Sterling', '826'),
            'BGN' => array('Bulgarian Lev', '975'),
            'KHR' => array('Cambodia Riel', '116'),
            'CAD' => array('Canadian Dollars', '124'),
            'KYD' => array('Cayman Islands Dollar', '136'),
            'CLP' => array('Chilean Peso', '152'),
            'CNY' => array('Chinese Renminbi Yuan', '156'),
            'COP' => array('Colombian Peso', '170'),
            'CRC' => array('Costa Rican Colon', '188'),
            'HRK' => array('Croatia Kuna', '191'),
            'CPY' => array('Cypriot Pounds', '196'),
            'CZK' => array('Czech Koruna', '203'),
            'DKK' => array('Danish Krone', '208'),
            'DOP' => array('Dominican Republic Peso', '214'),
            'XCD' => array('East Caribbean Dollar', '951'),
            'EGP' => array('Egyptian Pound', '818'),
            'ERN' => array('Eritrean Nakfa', '232'),
            'EEK' => array('Estonia Kroon', '233'),
            'EUR' => array('Euro', '978'),
            'GEL' => array('Georgian Lari', '981'),
            'GHC' => array('Ghana Cedi', '288'),
            'GIP' => array('Gibraltar Pound', '292'),
            'GTQ' => array('Guatemala Quetzal', '320'),
            'HNL' => array('Honduras Lempira', '340'),
            'HKD' => array('Hong Kong Dollars', '344'),
            'HUF' => array('Hungary Forint', '348'),
            'ISK' => array('Icelandic Krona', '352'),
            'INR' => array('Indian Rupee', '356'),
            'IDR' => array('Indonesia Rupiah', '360'),
            'ILS' => array('Israel Shekel', '376'),
            'JMD' => array('Jamaican Dollar', '388'),
            'JPY' => array('Japanese yen', '392'),
            'KZT' => array('Kazakhstan Tenge', '368'),
            'KES' => array('Kenyan Shilling', '404'),
            'KWD' => array('Kuwaiti Dinar', '414'),
            'LVL' => array('Latvia Lat', '428'),
            'LBP' => array('Lebanese Pound', '422'),
            'LTL' => array('Lithuania Litas', '440'),
            'MOP' => array('Macau Pataca', '446'),
            'MKD' => array('Macedonian Denar', '807'),
            'MGA' => array('Malagascy Ariary', '969'),
            'MYR' => array('Malaysian Ringgit', '458'),
            'MTL' => array('Maltese Lira', '470'),
            'BAM' => array('Marka', '977'),
            'MUR' => array('Mauritius Rupee', '480'),
            'MXN' => array('Mexican Pesos', '484'),
            'MZM' => array('Mozambique Metical', '508'),
            'NPR' => array('Nepalese Rupee', '524'),
            'ANG' => array('Netherlands Antilles Guilder', '532'),
            'TWD' => array('New Taiwanese Dollars', '901'),
            'NZD' => array('New Zealand Dollars', '554'),
            'NIO' => array('Nicaragua Cordoba', '558'),
            'NGN' => array('Nigeria Naira', '566'),
            'KPW' => array('North Korean Won', '408'),
            'NOK' => array('Norwegian Krone', '578'),
            'OMR' => array('Omani Riyal', '512'),
            'PKR' => array('Pakistani Rupee', '586'),
            'PYG' => array('Paraguay Guarani', '600'),
            'PEN' => array('Peru New Sol', '604'),
            'PHP' => array('Philippine Pesos', '608'),
            'QAR' => array('Qatari Riyal', '634'),
            'RON' => array('Romanian New Leu', '946'),
            'RUB' => array('Russian Federation Ruble', '643'),
            'SAR' => array('Saudi Riyal', '682'),
            'CSD' => array('Serbian Dinar', '891'),
            'SCR' => array('Seychelles Rupee', '690'),
            'SGD' => array('Singapore Dollars', '702'),
            'SKK' => array('Slovak Koruna', '703'),
            'SIT' => array('Slovenia Tolar', '705'),
            'ZAR' => array('South African Rand', '710'),
            'KRW' => array('South Korean Won', '410'),
            'LKR' => array('Sri Lankan Rupee', '144'),
            'SRD' => array('Surinam Dollar', '968'),
            'SEK' => array('Swedish Krona', '752'),
            'CHF' => array('Swiss Francs', '756'),
            'TZS' => array('Tanzanian Shilling', '834'),
            'THB' => array('Thai Baht', '764'),
            'TTD' => array('Trinidad and Tobago Dollar', '780'),
            'TRY' => array('Turkish New Lira', '949'),
            'AED' => array('UAE Dirham', '784'),
            'USD' => array('US Dollars', '840'),
            'UGX' => array('Ugandian Shilling', '800'),
            'UAH' => array('Ukraine Hryvna', '980'),
            'UYU' => array('Uruguayan Peso', '858'),
            'UZS' => array('Uzbekistani Som', '860'),
            'VEB' => array('Venezuela Bolivar', '862'),
            'VND' => array('Vietnam Dong', '704'),
            'AMK' => array('Zambian Kwacha', '894'),
            'ZWD' => array('Zimbabwe Dollar', '716'),
        );

        $this->countriesBlocked = array('AF' => 'Afghanistan', 'KP' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF'); //apply rules on top


        $this->countries = array(
            'AF' => 'Afghanistan',
            'AX' => 'Åland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia and Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CD' => 'Zaire',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Côte D\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands (Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island and Mcdonald Islands',
            'VA' => 'Vatican City State',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran, Islamic Republic of',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IM' => 'Isle of Man',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'KENYA',
            'KI' => 'Kiribati',
            'KR' => 'Korea, Republic of',
            'KP' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Lao People\'s Democratic Republic',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia, the Former Yugoslav Republic of',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia, Federated States of',
            'MD' => 'Moldova, Republic of',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestinian Territory, Occupied',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Réunion',
            'RO' => 'Romania',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts and Nevis',
            'LC' => 'Saint Lucia',
            'PM' => 'Saint Pierre and Miquelon',
            'VC' => 'Saint Vincent and the Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome and Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia and the South Sandwich Islands',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard and Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'TW' => 'Taiwan, Province of China',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania, United Republic of',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad and Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks and Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UM' => 'United States Minor Outlying Islands',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Viet Nam',
            'VG' => 'Virgin Islands, British',
            'VI' => 'Virgin Islands, U.S.',
            'WF' => 'Wallis and Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        );

    }

    /**
     * @return bool
     */
    public function adminCheck()
    {
        if ($this->user->user_type == 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Change if the customer is eligible to use the billing / credit system
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function qualify()
    {

        $country = request()->get('country_code');
        $price = request()->get('amount');

        $responseTime = microtime(true) - LARAVEL_START;


        if (!key_exists($country, $this->countries)) {

            $this->log->insertLog(
                "qualify-check",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'error' => "invalid country",
            ], 400);
        }


        if (key_exists($country, $this->countriesBlocked)) {

            $this->log->insertLog(
                "qualify-check",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => false,
            ], 200);
        }

        if ($price < 100) {

            $this->log->insertLog(
                "qualify-check",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => false,
            ], 200);
        }


        return response()->json([
            'status' => true,
        ], 200);

    }

    /**
     * Get instalment plans recommended by Bizpay
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function instalments()
    {
        $plans = $this->bizpay->instalments(
            request()->get('price'),
            request()->get('tax') / 100,
            (1 + request()->get('price-hike') / 100),
            request()->get('first-payment')
        );

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "instalments",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );


        return response()->json([
            'plans' => $plans,
        ], 200);
    }


    /**
     * Add new buyer and link it to the merchant
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUser()
    {
        $customer = $this->api->checkUserExistsForMerchant(request()->get('email'), $this->user->merchant_id);


        if (is_null($customer)) {

            $firstName = request()->get('first_name');
            $lastName = request()->get('last_name');
            $email = request()->get('email');
            $addressLine1 = request()->get('address_line_1');
            $addressLine2 = request()->get('address_line_2');
            $city = request()->get('city');
            $postCode = request()->get('postcode');
            $phoneNumber = request()->get('phone');
            $country = strtoupper(request()->get('country'));
            $organisationName = request()->get('company_name');
            $role = request()->get('position');
            $companyNumber = request()->get('company_number');
            $jobTitle = request()->get('occupation');
            $name = $firstName . " " . $lastName;


            if (!key_exists($country, $this->countries)) {
                return response()->json([
                    'error' => "invalid country",
                ], 400);
            }


            if (key_exists($country, $this->countriesBlocked)) {
                return response()->json([
                    'error' => "customers from this country are not allowed to use bizpay",
                ], 400);
            }


            if (strlen($name) < 2 || strlen($email) < 4) {
                $responseTime = microtime(true) - LARAVEL_START;

                $this->log->insertLog(
                    "create-user",
                    json_encode(request()->all()),
                    $responseTime,
                    '400',
                    $this->user->id,
                    $this->user->merchant_id
                );

                return response()->json([
                    'error' => "invalid data",
                ], 400);
            } else {
                $user = $this->bizpay->addUser(
                    $this->user->merchant_id,
                    $name,
                    $email,
                    $firstName,
                    $lastName,
                    $addressLine1,
                    $addressLine2,
                    $city,
                    $postCode,
                    $country,
                    $organisationName,
                    $companyNumber,
                    $role,
                    $phoneNumber,
                    $jobTitle
                );

                $responseTime = microtime(true) - LARAVEL_START;

                $this->log->insertLog(
                    "create-user",
                    json_encode(request()->all()),
                    $responseTime,
                    '200',
                    $this->user->id,
                    $this->user->merchant_id
                );

                return response()->json([
                    'status' => "user created",
                    'user_id' => $user->user_id
                ], 200);
            }


        } else {
            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "create-user",
                json_encode(request()->all()),
                $responseTime,
                '400',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => "user already exists",
                'user_id' => $customer->user_id
            ], 403);
        }
    }

    /**
     *
     *  Get User Details
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($id)
    {
        try {
            $user = $this->bizpay->getUser($id, $this->user->merchant_id)[0];
            unset($user['id']);
            unset($user['api_token']);
            unset($user['user_type']);
            unset($user['confirmed']);
            unset($user['confirmation_code']);
            unset($user['merchant_id']);
            unset($user['status']);
            unset($user['created_at']);
            unset($user['updated_at']);
            unset($user['api_limit']);
            unset($user['api_usage']);
            $user['customer_id'] = $user['user_id'];
            unset($user['user_id']);

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "read-user",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                "customer" => $user
            ], 200);
        } catch (\Exception $exception) {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "read-user",
                json_encode(request()->all()),
                $responseTime,
                '404',
                $this->user->id,
                $this->user->merchant_id
            );


            return response()->json([
                "status" => "There is no such user linked to your merchant account"
            ], 404);
        }

    }

    /**
     * Update customer
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser($id)
    {
        $firstName = request()->get('first_name');
        $lastName = request()->get('last_name');
//        $email = request()->get('email');
        $addressLine1 = request()->get('address_line_1');
        $addressLine2 = request()->get('address_line_2');
        $addressLine3 = request()->get('address_line_3');
        $city = request()->get('city');
        $postCode = request()->get('postcode');
        $phoneNumber = request()->get('phone');
        $country = strtoupper(request()->get('country'));
        $organisationName = request()->get('company_name');
        $role = request()->get('position');
        $companyNumber = request()->get('company_number');
        $jobTitle = request()->get('occupation');
        $name = $firstName . " " . $lastName;


        if (!key_exists($country, $this->countries)) {
            return response()->json([
                'error' => "invalid country",
            ], 400);
        }


        if (key_exists($country, $this->countriesBlocked)) {
            return response()->json([
                'error' => "customers from this country are not allowed to use bizpay",
            ], 400);
        }


        try {
            $user = $this->bizpay->updateUser(
                $id,
                $this->user->merchant_id,
                $name,
                $firstName,
                $lastName,
                $addressLine1,
                $addressLine2,
                $addressLine3,
                $city,
                $postCode,
                $country,
                $organisationName,
                $companyNumber,
                $phoneNumber,
                $jobTitle,
                $role
            );

            unset($user['id']);
            unset($user['api_token']);
            unset($user['user_type']);
            unset($user['confirmed']);
            unset($user['confirmation_code']);
            unset($user['merchant_id']);
            unset($user['status']);
            unset($user['created_at']);
            unset($user['updated_at']);
            unset($user['api_limit']);
            unset($user['api_usage']);

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "update-user",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                $user
            ], 200);

        } catch (\Exception $exception) {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "update-user",
                json_encode(request()->all()),
                $responseTime,
                '404',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                "status" => "There is no such user linked to your merchant account"
            ], 404);
        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsersForMerchant()
    {
        $users = $this->bizpay->getAllUsersForMerchant($this->user->merchant_id);

        foreach ($users as $user) {
            unset($user['id']);
            unset($user['api_token']);
            unset($user['user_type']);
            unset($user['confirmed']);
            unset($user['confirmation_code']);
            unset($user['merchant_id']);
            unset($user['status']);
            unset($user['created_at']);
            unset($user['updated_at']);

            $user['customer_id'] = $user['user_id'];
            unset($user['user_id']);
        }

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "read-all-users",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        if (count($users) > 0) {

            return response()->json([
                'customers' => $users
            ], 200);
        } else {
            return response()->json([
                'status' => "there are no users linked your merchant account"
            ], 200);
        }
    }


    /**
     * Make a charge against client's card
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function chargeClient()
    {
        $customerId = request()->get('customer_id');
        $customer = $this->api->GetUserFromUserIdForMerchant($customerId, $this->user->merchant_id);

        $amount = request()->get('amount');
        $currency = request()->get('currency-code');
        $description = request()->get('description');
        $deferredPaymentRef = request()->get('deferred-payment-ref');
        $gateway = request()->get('gateway');
        $orderRef = request()->get('order-ref');


        if (request()->get('gateway') == "stripe") {
            $gateway = 1;
        }

        if (request()->get('gateway') == "gocardless") {
            $gateway = 2;
        }


        if (!($gateway == 1 || $gateway == 2)) {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "charge-customer",
                json_encode(request()->all()),
                $responseTime,
                '401',
                $this->user->id,
                $this->user->merchant_id
            );

            exit(response()->json([
                "error" => 'invalid gateway'
            ], 401));
        }


        if ($customer->merchant_id != $this->user->merchant_id) {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "charge-customer",
                json_encode(request()->all()),
                $responseTime,
                '401',
                $this->user->id,
                $this->user->merchant_id
            );


            exit(response()->json([
                "error" => "Invalid Request"
            ], 401));
        } else {

            $chargeId = $this->bizpay->chargeClient($customer, $amount, $currency, $description, $gateway, 0,
                $deferredPaymentRef, $orderRef);
        }


        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "charge-customer",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'status' => "Charged",
            'charge_id' => $chargeId,
        ], 200);
    }

    /**
     * Schedule a payment against's buyer card on a future date
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deferredPayment()
    {

        $amount = request()->get('amount');
        $gateway = request()->get('gateway');
        $currencyCode = request()->get('currency_code');
        $description = request()->get('description');
        $customerId = request()->get('customer_id');
        $customer = $this->api->GetUserFromUserIdForMerchant($customerId, $this->user->merchant_id);

        $instalments = request()->get('instalments');


        $date = request()->get('payment_date');

        $formattedDate = Carbon::parse($date);
        $tax = request()->get('tax');
        $duration = request()->get('duration');
        $orderId = request()->get('order_id');
        $orderRef = request()->get('order_ref');

        if (request()->get('order_type') == "instalments") {
            $orderType = 2;
        } else {
            $orderType = 1;
        }

        if ($gateway == "stripe") {
            $gateway = 1;
        } elseif ($gateway == "gocardless") {
            $gateway = 2;
        } else {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "create-deferred-payment",
                json_encode(request()->all()),
                $responseTime,
                '401',
                $this->user->id,
                $this->user->merchant_id
            );

            exit(response()->json([
                "error" => 'invalid gateway'
            ], 401));
        }

        $now = Carbon::now();


        if ($now->toDateString() == $formattedDate->toDateString()) {
            $formattedDate->addHours(23);
            $formattedDate->addMinutes(59);
        }


        if ($now > $formattedDate) {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "create-deferred-payment",
                json_encode(request()->all()),
                $responseTime,
                '403',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => "Invalid",
                'payment_status' => "Failed",
                'details' => "You requested payment to be taken " . $formattedDate->diffForHumans() . "",
            ], 403);
        } else {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "create-deferred-payment",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );


            $testCheck = env('API_ENV');

            if ($testCheck == "test") {
                $testCheck = 1;
            } else {
                $testCheck = 0;
            }


            $this->bizpay->deferredPayment(
                $customer,
                $date,
                $amount,
                $tax,
                $gateway,
                $this->user->merchant_id,
                $currencyCode,
                $description,
                $orderType,
                $instalments,
                $orderRef,
                $duration,
                $orderId,
                $testCheck
            );

            return response()->json([
                'status' => "Success",
                'payment_status' => "Requested",
                'details' => "Next Payment : " . $formattedDate->diffForHumans() . "",
            ], 200);
        }
    }

    /**
     * Add buyer to a subscription plan
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setUpSubscription()
    {
        $customerEmail = request()->get('client-email');
        $customer = $this->api->getUserForMerchant($customerEmail);
        $planId = request()->get('plan-id');
        $taxPercent = request()->get('tax-percent');
        $recurringPayment = request()->get('recurring-payment');
        $currency = request()->get('currency-code');

        $subscriptionId = $this->bizpay->addSubscription(
            $customer,
            $planId,
            $taxPercent,
            $recurringPayment,
            $currency
        );

        return response()->json([
            'status' => "Success",
            'plan_id' => $subscriptionId
        ], 200);
    }

    /**
     * Setup Instalment Plan
     */
    public function setUpInstalment()
    {
        $planDescription = request()->get('plan-description');
        $currencyCode = request()->get('currency-code');
        $firstPayment = request()->get('first-payment');
        $firstDate = request()->get('first-date');
        $recurringPayment = request()->get('recurring-payment');

        $orderId = request()->get('order-id');
        $orderId = Order::OrderFromSlug($orderId)[0]->id;
        $orderType = request()->get('order-type');

        if ($orderType == "subscription") {
            $orderType = 2;
        }

        if ($orderType == "instalment") {
            $orderType = 1;
        }

        $duration = request()->get('duration');
        $instalments = request()->get('instalments');
        $tax = request()->get('tax');
        $gateway = request()->get('gateway');

        $customerEmail = request()->get('client-email');
        $customer = $this->api->getUserForMerchant($customerEmail);
        $formattedFirstDate = Carbon::parse($firstDate);

        if ($gateway == "stripe") {
            $gateway = 1;
        } elseif ($gateway == "gocardless") {
            $gateway = 2;
        } else {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "setup-instalment",
                json_encode(request()->all()),
                $responseTime,
                '401',
                $this->user->id,
                $this->user->merchant_id
            );

            exit(response()->json([
                "error" => 'invalid gateway'
            ], 401));
        }

        $now = Carbon::now();

        if ($orderType == "instalment") {
            if ($now < $formattedFirstDate) {
                $this->bizpay->deferredPayment(
                    $customer,
                    $formattedFirstDate,
                    $firstPayment,
                    $tax,
                    $gateway,
                    $this->user->merchant_id,
                    $currencyCode,
                    $planDescription
                );
            } else {
                $this->bizpay->chargeClient(
                    $customer,
                    $firstPayment,
                    $currencyCode,
                    $planDescription,
                    $gateway,
                    $tax
                );
            }


            for ($i = 0; $i < $instalments; $i++) {
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

                $this->bizpay->deferredPayment(
                    $customer,
                    $formattedFirstDate,
                    $recurringPayment,
                    $tax,
                    $gateway,
                    $this->user->merchant_id,
                    $currencyCode,
                    $planDescription
                );

            }
        } elseif ($orderType == "subscription") {
            if ($now < $formattedFirstDate) {
                $this->bizpay->deferredPayment(
                    $customer,
                    $formattedFirstDate,
                    $firstPayment,
                    $tax,
                    $gateway,
                    $this->user->merchant_id,
                    $currencyCode,
                    $planDescription,
                    $orderId,
                    $orderType,
                    $duration

                );
            } else {
                $this->bizpay->chargeClient(
                    $customer,
                    $firstPayment,
                    $currencyCode,
                    $planDescription,
                    $gateway,
                    $tax
                );

                $this->bizpay->deferredPayment(
                    $customer,
                    $formattedFirstDate,
                    $firstPayment,
                    $tax,
                    $gateway,
                    $this->user->merchant_id,
                    $currencyCode,
                    $planDescription,
                    $orderId,
                    $orderType,
                    $duration

                );
            }
        }

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "setup-instalment",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'status' => "Success"
        ], 200);
    }


    /**
     * Set up a plan
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setUpPlan()
    {
        $this->bizpay->addPlan(
            $this->user,
            request()->get('recurring-payment'),
            request()->get('currency-code'),
            request()->get('plan-id'),
            request()->get('name'),
            request()->get('billing-frequency'), //day, month,year,week,3-month,6-month
            request()->get('trial-days')
        );

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "setup-bizpay-plan",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'status' => "Success"
        ]);
    }

    /**
     * Allow merchant to request a custom plan based on params
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function customPlan()
    {
        $hike = 1 + request()->get('change') / 100;

        if (request()->get('months') < 1) {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "setup-custom-plan",
                json_encode(request()->all()),
                $responseTime,
                '403',
                $this->user->id,
                $this->user->merchant_id
            );


            return response()->json([
                'months' => "Invalid"
            ], 403);

        } else {
            $plans = $this->bizpay->customPlans(
                request()->get('amount') * $hike,
                request()->get('first_payment'), //in percent
                request()->get('months')
            );

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "setup-custom-plan",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'plans' => $plans
            ], 200);
        }
    }

    /**
     * Check rules to find what instalment durations are valid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allowedInstalmentPlans()
    {
        $rules = new Rules($this->user->merchant_id);
        $planMonths = $rules->planMonths();

        return response()->json([
            'allowed_months' => $planMonths,
        ], 200);
    }

    /**
     * Add merchant rule
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRule()
    {
        $name = request()->get('name');
        $checkType = request()->get('check-type');
        $applyRuleOn = request()->get('apply-rule-on');
        $description = request()->get('description');
        $dataType = request()->get('data-type');
        $limit1 = request()->get('param-1');
        $limit2 = request()->get('param-2');
        $limit3 = request()->get('param-3');
        $actionOn = request()->get('action-on');
        $actionType = request()->get('action-type');
        $actionValue = request()->get('action-value');
        $merchantIdon = null;

        $rule = new Rules($this->user->merchant_id);
        $rule->addRule(
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
            $this->user->id
        );

        return response()->json([
            'status' => 'rule added',
        ], 200);
    }

    /**
     * Cancel an active subscription
     *
     * @param $subscriptionId
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function deleteSubscription($subscriptionId)
    {
        $customerEmail = request()->get('client-email');
        $customer = $this->api->getUserForMerchant($customerEmail);
        $this->bizpay->cancelSubscription(
            $customer,
            $subscriptionId
        );

        return response()->json([
            'status' => 'subscription cancelled',
        ]);
    }

    /**
     * Refund a payment
     *
     * @param $paymentid
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function refundCharge($paymentid)
    {
        $customerEmail = request()->get('client-email');
        $customer = $this->api->getUserForMerchant($customerEmail);
        $this->bizpay->refund($customer, $paymentid);

        return response()->json([
            'status' => 'charge refunded',
        ]);
    }

    /**
     * Add (or Update) a new card based on the token receiving my the payment gateway
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCard()
    {
        $customerEmail = request()->get('client-email');
        $gateway = request()->get('gateway');
        $customer = $this->api->getUserForMerchant($customerEmail);

        // TODO: Add this check to other methods
        if ($gateway == "stripe") {
            $this->bizpay->addCardToStripe($customer, request()->get('stripe-token'));
        }

        return response()->json([
            'status' => 'card added',
        ]);
    }

    /**
     * Return stripe public credential
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stripePublicCredential()
    {
        $credential = $this->bizpay->stripePublicCredential($this->user);
        return response()->json([
            'public-key' => $credential,
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function products()
    {
        $products = $this->bizpay->products($this->user->merchant_id);

        foreach ($products as $product) {
            unset($product['id']);
            $product['user_id'] = User::findorFail($product['user_id'])->user_id;

            if ($product['type'] == 1) {
                $product['type'] = "one-off";
            } elseif ($product['type'] == 2) {
                $product['type'] = "subscription";
            } elseif ($product['type'] == 3) {
                $product['type'] = "instalment";
            }


            if ($product['status'] == 1) {
                $product['status'] = "active";
            } else {
                $product['status'] = "in-active";
            }

        }
        return response()->json([
            'products' => $products,
        ], 200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function allPayments()
    {
        $payments = $this->bizpay->allPayments();

        if ($this->adminCheck()) {
            return response()->json([
                'payments' => $payments,
            ], 200);
        } else {
            return response()->json([
                'error' => 'End-point available only to admin!',
            ], 403);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function payments()
    {
        $payments = $this->bizpay->payments($this->user->merchant_id)->toArray();

        foreach ($payments as $payment) {
            unset($payment['id']);
            unset($payment['merchant_id']);
            unset($payment['status_text']);
            unset($payment['status']);
            $payment['user_id'] = User::findorFail($payment['user_id'])->user_id;
            $payment['order_id'] = Order::findorFail($payment['order_id'])->user_id;
        }
        return response()->json([
            'payments' => $payments,
        ]);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscriptions()
    {
        $subscriptions = $this->bizpay->subscriptions($this->user->merchant_id)->toArray();

        foreach ($subscriptions as $subscription) {
            unset($subscription['id']);

            if ($subscription['payment_gateway_id'] == 1) {
                $subscription['payment_gateway_id'] = "stripe";
            } else {
                $subscription['payment_gateway_id'] = "inactive";
            }

            $payment['user_id'] = User::findorFail($subscription['user_id'])->user_id;


            if ($subscription['status'] == 1) {
                $subscription['status'] = "active";
            } else {
                $subscription['status'] = "in-active";
            }
        }
        return response()->json([
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function allSubscriptions()
    {
        $subscriptions = $this->bizpay->allSubscriptions();

        if ($this->adminCheck()) {
            return response()->json([
                'subscriptions' => $subscriptions,
            ]);
        } else {
            return response()->json([
                'error' => 'End-point available only to admin!',
            ]);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function allOrders()
    {
        $orders = $this->bizpay->allOrders();

        if ($this->adminCheck()) {
            return response()->json([
                'orders' => $orders,
            ]);
        } else {
            return response()->json([
                'error' => 'End-point available only to admin!',
            ]);
        }
    }

    public function orders()
    {
        $orders = $this->bizpay->orders($this->user->merchant_id)->toArray();

        foreach ($orders as $order) {
            unset($order['id']);
            unset($order['status']);
            $order['user_id'] = User::findorFail($order['user_id'])->user_id;
        }

        return response()->json([
            'orders' => $orders,
        ]);
    }


    public function deleteRule()
    {
    }


    /**
     * @return mixed
     */
    public function allFailedPayments()
    {
        $failedPayments = $this->bizpay->allFailedPayments()->toArray();
        return response()->json([
            'failed_payments' => $failedPayments,
        ]);
    }

    /**
     * @return mixed
     */
    public function failedPaymentsByMerchant()
    {
        $failedPayments = $this->bizpay->failedPaymentsByMerchant()->toArray();
        return response()->json([
            'failed_payments' => $failedPayments,
        ]);
    }

    /**
     * @return mixed
     */
    public function failedPaymentsByUser()
    {
        $failedPayments = $this->bizpay->failedPaymentsByUser()->toArray();
        return response()->json([
            'failed_payments' => $failedPayments,
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyPaymentToPlan()
    {
        $orderId = request()->get('order-id');
        $orderId = Order::OrderFromSlug($orderId)[0]->id;
        $instalments = request()->get('instalments');
        $this->bizpay->applyPaymentToPlan($orderId, $instalments);

        return response()->json([
            'status' => 'processed',
        ]);
    }


    public function updateInstalments($ref)
    {

        $orderRef = $ref;
        $instalments = request()->get('instalments');
        $firstInstalment = request()->get('first_check');

        if ($firstInstalment != "true") {
            $this->bizpay->updateInstalments($orderRef, $this->user->merchant_id, $instalments);

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "instalments-updated",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => 'recurring instalments updated',
            ], 200);
        } else {
            $this->bizpay->updateFirstInstalment($orderRef, $this->user->merchant_id);

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "instalments-updated",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => 'first instalment updated',
            ], 200);

        }
    }

    public function retryFailedPayments($ref)
    {
        $orderRef = $ref;
        $check = $this->bizpay->retryFailedPayments($orderRef, $this->user->merchant_id);

        if ($check) {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "retry-failed-payments",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => 'retrying failed payments succeeded',
            ], 200);
        } else {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "retry-failed-payments",
                json_encode(request()->all()),
                $responseTime,
                '403',
                $this->user->id,
                $this->user->merchant_id
            );


            return response()->json([
                'status' => 'retrying failed payments failed',
            ], 403);
        }


    }


    /**
     * Returns minimum customer age from rules
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clientMinimumAge()
    {
        $rule = new Rules($this->user->merchant_id);
        $response = $rule->clientMinimumAge();

        if(!$response===false){
            return response()->json([
                'client_age' => $response,
            ]);
        } else {
            return response()->json([
                'client_age' => "no set",
            ]);
        }



    }

    /**
     *  Returns minimum customer age set by admin from rules
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clientMinimumAgeSetByAdmin()
    {
        $rule = new Rules($this->user->merchant_id);
        $response = $rule->clientMinimumAgeSetByAdmin();
        return response()->json([
            'client_age_by_admin' => $response,
        ]);
    }


    /**
     * Returns minimum price for offering credit from rules
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function minimumPriceForCredit()
    {
        $rule = new Rules($this->user->merchant_id);
        $response = $rule->minimumPriceForPlan();

        if(!$response===false){
            return response()->json([
                'minimum_price_for_credit' => $response,
            ],200);
        } else {
            return response()->json([
                'minimum_price_for_credit' => 0.00,
            ],200);
        }

    }

    /**
     * Returns minimum price for offering credit set by admin from rules
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function minimumPriceForCreditByAdmin()
    {
        $rule = new Rules($this->user->merchant_id);
        $response = $rule->clientMinimumAgeSetByAdmin();
        return response()->json([
            'minimum_price_for_credit_by_admin' => $response,
        ]);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function disallowedCountries()
    {
        $rule = new Rules($this->user->merchant_id);
        $response = $rule->clientDisallowedCountries();
        return response()->json([
            'disallowed-countries' => $response,
        ]);
    }

    /**
     * Return trial days from rules
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function trialPeriod()
    {
        $rule = new Rules($this->user->merchant_id);
        $response = $rule->trialPeriod();
        return response()->json([
            'trial-duration' => $response,
        ]);
    }

    /**
     * Returns list of all users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allUsers()
    {
        if ($this->adminCheck()) {
            $users = $this->bizpay->allUsers();
            return response()->json([
                'users' => $users,
            ]);
        } else {
            return response()->json([
                'error' => 'End-point available only to admin!',
            ]);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function addGCCustomer()
    {

        $customerId = request()->get('customer_id');
        $customer = $this->api->GetUserFromUserIdForMerchant($customerId, $this->user->merchant_id);

        $countryCode = request()->get('country-code');
        $orderId = request()->get('order-id');
        $accountNumber = request()->get('account-number');
        $sortCode = request()->get('sort-code');
        $iban = request()->get('iban');

        $this->bizpay->addGCCustomerAndBankAccount(
            $customer,
            $countryCode,
            $orderId,
            $accountNumber,
            $sortCode,
            $iban
        );

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "add-gc-customer",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'status' => 'customer-added',
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function chargeGCCustomer()
    {
        $customerId = request()->get('customer_id');
        $customer = $this->api->GetUserFromUserIdForMerchant($customerId, $this->user->merchant_id);

        $amount = request()->get('amount');
        $orderId = request()->get('order-id');
        $currencyCode = request()->get('currency-code');
        $deferredPaymentRef = request()->get('deferred-payment-ref');

        $paymentId = $this->bizpay->chargeGCCustomer($customer, $amount, $currencyCode, $orderId, $deferredPaymentRef);

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "charge-gc-customer",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'status' => 'payment processed',
            'payment-id' => $paymentId,
        ], 200);
    }

    /**
     * Add GoCardless Subscription
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addGCSubscription()
    {
        $customerEmail = request()->get('client-email');
        $customer = $this->api->getUserForMerchant($customerEmail);
        $duration = request()->get('frequency');
        $day = request()->get('day'); // day of month
        $count = request()->get('count'); //$count
        $amount = request()->get('amount');
        $orderId = request()->get('order-id');
        $currencyCode = request()->get('currency-code');

        $subscriptionId = $this->bizpay->addGCSubscription(
            $customer,
            $amount,
            $currencyCode,
            $duration,
            $day,
            $count,
            $orderId
        );

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "subscription-added",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'status' => 'subscription added',
            'subscription-id' => $subscriptionId,
        ], 200);
    }

    /**
     * Cancel a gocardless subscription
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelGCSubscription()
    {
        $customerEmail = request()->get('client-email');
        $customer = $this->api->getUserForMerchant($customerEmail);
        $subscriptionId = request()->get('subscription-id');
        $this->bizpay->cancelGCSubscription($subscriptionId);

        return response()->json([
            'status' => 'subscription cancelled',
        ]);
    }

    /**
     * Refund a Gocardless payment - full or partial
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refundGCCharge()
    {
        $customerEmail = request()->get('client-email');
        $customer = $this->api->getUserForMerchant($customerEmail);
        $refundAmount = request()->get('refund-amount');
        $totalAmount = request()->get('total-amount');
        $paymentId = request()->get('payment-id');

        $this->bizpay->processGCRefund($customer, $refundAmount, $totalAmount, $paymentId);

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "refund-gc-charge",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'status' => 'payment refunded'
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateGCRedirectURL()
    {
        $customerId = request()->get('customer_id');
        $customer = $this->api->GetUserFromUserIdForMerchant($customerId, $this->user->merchant_id);
        $successURL = request()->get('success-url');
        $description = request()->get('description');
        $url = $this->bizpay->getGCRedirectURL($customer, $description, $successURL);

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "gc-redirect-url",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'redirect_url' => $url
        ], 200);
    }

    public function addProduct()
    {
    }


    public function removeProductFromShop()
    {
    }

    /**
     * Post API dev - seperate this out
     */
    public function addOrder()
    {
    }

    public function addPayment()
    {
    }

    public function terms()
    {
    }


    public function makePaymentTowardsInstalment()
    {
    }

    public function getInstalmentPlanBalance()
    {
    }

    public function addStripeCustomer()
    {
//        $customerEmail = request()->get('client-email');
//        $customer = $this->api->getUserForMerchant($customerEmail);

        //  dd( request()->get('customer_id'));
        $customerId = request()->get('customer_id');
        $customer = $this->api->GetUserFromUserIdForMerchant($customerId, $this->user->merchant_id);

        $stripeToken = request()->get('stripe_token');
        $this->bizpay->addCardToStripe($customer, $stripeToken);

        return response()->json([
            'status' => 'stripe customer created'
        ], 200);

    }



    public function createOrder()
    {
        $customerId = request()->get('customer_id');
        $customer = $this->api->GetUserFromUserIdForMerchant($customerId, $this->user->merchant_id);

        $planDetails = request()->get('plan-details');
        $vendorRef = request()->get('vendor-ref');
        $currencyCode = request()->get('currency-code');
        $tax = request()->get('tax');
        $price = request()->get('price');
        $firstPayment = request()->get('first-payment');
        $balance = request()->get('balance');
        $instalments = request()->get('instalments');
        $products = json_decode(request()->get('products'));

        $orderId = $this->bizpay->createOrder($planDetails, $vendorRef, $currencyCode, $tax, $price, $firstPayment,
            $balance, $instalments, $products, $customer->id, $this->user->merchant_id);

        return response()->json([
            'order-id' => $orderId
        ]);
    }

    /**
     * test - not in use
     * remove post MVP
     *
     */
    public function retryCharges()
    {
        $ref = request()->get('order-ref');
        $payments = $this->bizpay->getPaymentInformation($ref, $this->user->merchant_id);

        foreach ($payments as $payment) {
            if ($payment->status == -1) {

                // calculate missed instalments
                //

            }
        }
    }


    public function cancelDeferredCharges($ref)
    {
        $this->bizpay->cancelDeferredCharges($ref, $this->user->merchant_id);

        return response()->json([
            'status' => 'agreement cancelled'
        ]);

    }

    public function refundPayments($ref)
    {
        $percent = request()->get('percent');

        if($percent>0){

            try{
                $this->bizpay->refundAgreement($ref, $this->user->merchant_id, $percent);

            } catch (\Exception $e){

            }

            return response()->json([
                'status' => $percent . '% refunded'
            ],200);
        } else {
            return response()->json([
                'error' => 'please specify the refund percentage'
            ],400);
        }


    }

    public function addGoCardlessRedirectClientCredentials()
    {
        $customerId = request()->get('customer_id');
        $redirectId = request()->get('redirect-id');

        $customer = $this->api->GetUserFromUserIdForMerchant($customerId, $this->user->merchant_id);

        $this->bizpay->goCardlessCredentials($redirectId, $this->user->merchant_id, $customer);

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "add-gc-credentials",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'status' => 'credentials added'
        ], 200);

    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function createProduct()
    {
        $productName = request()->get('name');
        $currency = strtoupper(request()->get('currency_code'));
        $tax = request()->get('tax_percent');
        $tags = request()->get('tags');
        $price = request()->get('price');
        $productId = request()->get('product_sku');
        $quantity = request()->get('quantity');
        $description = request()->get('description');


        if (!key_exists($currency, $this->currencies)) {
            return response()->json([
                'error' => "invalid currency",
            ], 400);
        }


        if (!($price > 0)) {
            return response()->json([
                'error' => "invalid price",
            ], 400);
        }


        if (strlen($productName) < 1 || strlen($currency) < 1 || strlen($price) < 1) {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "create-product",
                json_encode(request()->all()),
                $responseTime,
                '400',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'error' => 'invalid or missing parameters'
            ], 400);
        } else {
            $product = $this->app->createProduct(
                $productName,
                $currency,
                $tax,
                $price,
                $this->user->id,
                $this->user->merchant_id,
                $productId,
                $quantity,
                $description,
                $tags
            );

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "create-product",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => 'product created',
                'product-id' => $product->slug
            ], 200);
        }
    }

    /**
     * Get product - by id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProduct($id)
    {
        $slug = $id;
        $product = $this->app->getProduct($slug, $this->user->merchant_id);

        unset($product['id']);
        unset($product['status']);
        unset($product['created_at']);
        unset($product['updated_at']);
        unset($product['updated_at']);
        unset($product['user_id']);
        unset($product['merchant_id']);
        unset($product['slug']);
        $product['product_sku'] = $product['product_id'];
        unset($product['product_id']);

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "read-product",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'product' => $product
        ], 200);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProduct($id)
    {

        $slug = $id;
        $name = request()->get('name');
        $currency = strtoupper(request()->get('currency_code'));
        $tax = request()->get('tax_percent');
        $tags = request()->get('tags');
        $price = request()->get('price');
        $productId = request()->get('product_sku');
        $quantity = request()->get('quantity');
        $description = request()->get('description');

        if (!key_exists($currency, $this->currencies)) {
            return response()->json([
                'error' => "invalid currency",
            ], 400);
        }


        if (!($price > 0)) {
            return response()->json([
                'error' => "invalid price",
            ], 400);
        }

        if (strlen($name) < 1 || strlen($currency) < 1 || strlen($price) < 1) {
            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "update-product",
                json_encode(request()->all()),
                $responseTime,
                '400',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'error' => 'invalid or missing parameters'
            ], 400);
        } else {
            $this->app->updateProduct(
                $slug,
                $price,
                $name,
                $currency,
                $tax,
                $this->user->merchant_id,
                $productId,
                $quantity,
                $description,
                $tags
            );

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "update-product",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => 'product updated'
            ], 200);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProduct($id)
    {
        $slug = $id;
        $this->app->deleteProduct($slug, $this->user->merchant_id);

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "delete-product",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'status' => 'product deleted'
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function allProducts()
    {
        $products = $this->app->allProducts($this->user->merchant_id);

        $responseTime = microtime(true) - LARAVEL_START;
        $response = "";

        $this->log->insertLog(
            "read-all-products",
            $response,
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        foreach ($products as $product) {
            unset($product["created_at"]);
            unset($product["updated_at"]);
            unset($product["status"]);
            unset($product["merchant_id"]);
            unset($product["user_id"]);
            unset($product["id"]);
            $product["product_sku"] = $product["product_id"];
            $product["product_id"] = $product["slug"];
            unset($product["slug"]);
        }

        return response()->json([
            'products' => $products,

        ], 200);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPlan()
    {

        $planName = request()->get('name');
        $structure = request()->get('plan_type');

        if (!($structure == "instalment" || $structure == "single")) {

            return response()->json([
                'error' => 'plan_type should be instalment or single'
            ], 400);

        }

        if ($structure == "instalment") {
            $structure = 2;
        } else {
            $structure = 1;
        }

        $instalments = request()->get('durations');
        $billingPeriod = request()->get('billing_period');

        switch ($billingPeriod) {
            case (is_null($billingPeriod)):
                $billingPeriod = "null";
                break;
            case "daily":
                $billingPeriod = 1;
                break;
            case "weekly":
                $billingPeriod = 2;
                break;
            case "biweekly":
                $billingPeriod = 7;
                break;
            case "monthly":
                $billingPeriod = 3;
                break;
            case "quarterly":
                $billingPeriod = 4;
                break;
            case "biyearly":
                $billingPeriod = 5;
                break;
            case "yearly":
                $billingPeriod = 6;
                break;
        }


        if ((!is_numeric($billingPeriod)) && $structure == 2) {
            return response()->json([
                'error' => 'valid billing period is required for instalments'
            ], 400);
        }

        $billingStart = request()->get('billing_start');

        $paymentInfoRequired = request()->get('payment_info_required');

        if ($paymentInfoRequired == "true") {
            $paymentInfoRequired = 1;
        } else {
            $paymentInfoRequired = 0;
        }

        $firstPayment = request()->get('first_payment_percent');

        if (request()->has('first_payment_percent')) {
            $differentFirstPayment = 1;
        } else {
            $differentFirstPayment = 0;
        }


        $firstPaymentDate = request()->get('first_payment_delay');
        $refundPercent = request()->get('refund_percent');


        $renewal = request()->get('recurring_billing_check');
        $agreementTerm = request()->get('agreement_term');
        $canCancel = request()->get('cancellation_check');
        $cancellationDays = request()->get('cancellation_days');
        $refundCheck = request()->get('refund_check');
        $terms = request()->get('terms_file');


        if ($canCancel == "true") {
            $canCancel = 1;
        } else {
            $canCancel = 0;
        }

        if ($refundCheck == "true") {
            $refundCheck = 1;
        } else {
            $refundCheck = 0;
        }


        if ($renewal == "true") {
            $renewal = 1;
        } else {
            $renewal = 0;
        }


        // [{"duration": 3, "change": 10, "default": true},{"duration": 6, "change": 20, "default": false}]

        if (strlen($instalments) > 2) {
            $instalments = json_decode($instalments);
        }


        if (strlen($planName) < 1 ||
            strlen($billingStart) < 1
            || count($instalments) < 1

        ) {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "create-plan",
                json_encode(request()->all()),
                $responseTime,
                '400',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'error' => 'invalid or missing parameters'
            ], 400);
        } else {

            $plan = $this->app->createPlan(
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
                $this->user->id,
                $instalments,
                $this->user->merchant_id
            );

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "create-plan",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => "plan created",
                'plan-id' => $plan->slug
            ], 200);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlan($id)
    {
        $plan = $this->app->getPlan($id, $this->user->merchant_id);

        if ($plan) {
            $responseTime = microtime(true) - LARAVEL_START;

            unset($plan["created_at"]);
            unset($plan["updated_at"]);
            $plan["customer_id"] = User::findorFail($plan["user_id"])->user_id;
            unset($plan["id"]);
            unset($plan["merchant_id"]);
            unset($plan["user_id"]);
            unset($plan["status"]);

            $plan["plan_id"] = $plan["slug"];
            unset($plan["slug"]);


            $plan["can_cancel"] = $plan["can_cancel"] > 0 ? true : false;
            $plan["refund_check"] = $plan["refund_check"] > 0 ? true : false;
            $plan["payment_info_required"] = $plan["payment_info_required"] > 0 ? true : false;
            $plan["renewal_check"] = $plan["renewal"] > 0 ? true : false;
            $plan["different_first_payment"] = $plan["different_first_payment"] > 0 ? true : false;
            $plan["structure"] = $plan["structure"] > 1 ? "instalment" : "single";


            $billingPeriod = array(
                "1" => "daily",
                "2" => "weekly",
                "3" => "monthly",
                "4" => "quarterly",
                "5" => "biyearly",
                "6" => "yearly",
                "7" => "biweekly",
            );

            $plan["billing_period"] = $billingPeriod[$plan["billing_period"]];


            unset($plan["renewal"]);


            $this->log->insertLog(
                "read-plan",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );
            return response()->json([
                'plan' => $plan,
            ], 200);
        } else {
            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "read-plan",
                json_encode(request()->all()),
                $responseTime,
                '404',
                $this->user->id,
                $this->user->merchant_id
            );
            return response()->json([
                'error' => "plan not found",
            ], 404);
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePlan($id)
    {

        $planName = request()->get('name');
        $structure = request()->get('plan_type');

        if (!($structure == "instalment" || $structure == "single")) {

            return response()->json([
                'error' => 'plan_type should be instalment or single'
            ], 400);

        }

        if ($structure == "instalment") {
            $structure = 2;
        } else {
            $structure = 1;
        }

        $instalments = request()->get('durations');
        $billingPeriod = request()->get('billing_period');

        switch ($billingPeriod) {
            case (is_null($billingPeriod)):
                $billingPeriod = "null";
                break;
            case $billingPeriod == "daily":
                $billingPeriod = 1;
                break;
            case $billingPeriod == "weekly":
                $billingPeriod = 2;
                break;
            case $billingPeriod == "biweekly":
                $billingPeriod = 7;
                break;
            case $billingPeriod == "monthly":
                $billingPeriod = 3;
                break;
            case $billingPeriod == "quarterly":
                $billingPeriod = 4;
                break;
            case $billingPeriod == "biyearly":
                $billingPeriod = 5;
                break;
            case $billingPeriod == "yearly":
                $billingPeriod = 6;
                break;
        }


        if ((!is_numeric($billingPeriod)) && $structure == 2) {
            return response()->json([
                'error' => 'valid billing period is required for instalments'
            ], 400);
        }

        $billingStart = request()->get('billing_start');

        $paymentInfoRequired = request()->get('payment_info_required');

        if ($paymentInfoRequired == "true") {
            $paymentInfoRequired = 1;
        } else {
            $paymentInfoRequired = 0;
        }

        $firstPayment = request()->get('first_payment_percent');

        if (request()->has('first_payment_percent')) {
            $differentFirstPayment = 1;
        } else {
            $differentFirstPayment = 0;
        }


        $firstPaymentDate = request()->get('first_payment_delay');
        $refundPercent = request()->get('refund_percent');


        $renewal = request()->get('recurring_billing_check');
        $agreementTerm = request()->get('agreement_term');
        $canCancel = request()->get('cancellation_check');
        $cancellationDays = request()->get('cancellation_days');
        $refundCheck = request()->get('refund_check');
        $terms = request()->get('terms_file');


        if ($canCancel == "true") {
            $canCancel = 1;
        } else {
            $canCancel = 0;
        }

        if ($refundCheck == "true") {
            $refundCheck = 1;
        } else {
            $refundCheck = 0;
        }


        if ($renewal == "true") {
            $renewal = 1;
        } else {
            $renewal = 0;
        }


        // [{"duration": 3, "change": 10, "default": true},{"duration": 6, "change": 20, "default": false}]

        if (strlen($instalments) > 2) {
            $instalments = json_decode($instalments);
        }


        if (strlen($planName) < 1 ||
            strlen($billingStart) < 1
            || count($instalments) < 1

        ) {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "update-plan",
                json_encode(request()->all()),
                $responseTime,
                '400',
                $this->user->id,
                $this->user->merchant_id
            );


            return response()->json([
                'error' => 'invalid or missing parameters'
            ], 400);
        } else {

            $this->app->updatePlan(
                $planName,
                $structure,
                $id,
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
                $this->user->merchant_id
            );

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "update-plan",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => "plan updated",
            ], 200);

        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePlan($id)
    {
        $this->app->deletePlan(
            $id,
            $this->user->merchant_id
        );

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "delete-plan",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'plan' => "plan deleted",
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function allPlans()
    {
        $plans = $this->app->allPlans($this->user->merchant_id);

        foreach ($plans as $plan) {
            unset($plan["created_at"]);
            unset($plan["updated_at"]);
            $plan["customer_id"] = User::findorFail($plan["user_id"])->user_id;
            unset($plan["id"]);
            unset($plan["merchant_id"]);
            unset($plan["user_id"]);
            unset($plan["status"]);

            $plan["plan_id"] = $plan["slug"];
            unset($plan["slug"]);


            $plan["can_cancel"] = $plan["can_cancel"] > 0 ? true : false;
            $plan["refund_check"] = $plan["refund_check"] > 0 ? true : false;
            $plan["payment_info_required"] = $plan["payment_info_required"] > 0 ? true : false;
            $plan["renewal_check"] = $plan["renewal"] > 0 ? true : false;
            $plan["different_first_payment"] = $plan["different_first_payment"] > 0 ? true : false;
            $plan["structure"] = $plan["structure"] > 1 ? "instalment" : "single";


            $billingPeriod = array(
                "1" => "daily",
                "2" => "weekly",
                "3" => "monthly",
                "4" => "quarterly",
                "5" => "biyearly",
                "6" => "yearly",
                "7" => "biweekly",
            );

            $plan["billing_period"] = $billingPeriod[$plan["billing_period"]];


            unset($plan["renewal"]);
        }

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "read-all-plans",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'plans' => $plans,
        ], 200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function createQuote()
    {

        //k9GvACVmc9EZnEG8m65K

        // product:   dvaDg0OAJIpRai4Cfeu6
        //plan:     Wz9Ofk5RkIdIuA75sTnu

        $quoteName = request()->get('name');
        $planId = request()->get('plan_id');
        $validityType = request()->get('expiry_type');
        $validity = request()->get('expiry_value');
        $confirmationUrl = request()->get('confirmation_url');

        $customerId = request()->get('customer_id');
        $products = request()->get('products');
        $purchaseLimit = null;

        if ($validityType == 3) {
            $purchaseLimit = $validity;
        }


        if (strlen($customerId) > 1) {
            $customer = $this->api->GetUserFromUserIdForMerchant($customerId, $this->user->merchant_id);
            $customerId = $customer->id;
        }


        //      [{"id":"k9GvACVmc9EZnEG8m65K","quantity":1},{"id":"sdj910Bkkagk8d","quantity":1}],


        if (strlen($products) > 0) {
            $products = json_decode($products);

        }


        switch ($validityType) {
            case 0:
                $validity = null;
                break;
            case 1:
                if (!strtotime($validity)) {
                    return response()->json([
                        'error' => 'invalid expiry value - not valid date',
                    ], 422);
                }
                break;
            case 2:
                if (!($validity > 1)) {
                    return response()->json([
                        'error' => 'invalid expiry value',
                    ], 422);
                }
                break;
            case 3:
                if (!($validity > 1)) {
                    if (!($validity > 1)) {
                        return response()->json([
                            'error' => 'invalid expiry value',
                        ], 422);
                    }
                }
        }


        if (strlen($quoteName) < 1
        ) {
            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "create-quote",
                json_encode(request()->all()),
                $responseTime,
                '400',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'error' => 'invalid or missing parameters'
            ], 400);
        } else {


            $quote = $this->app->createQuote(
                $quoteName,
                $planId,
                $this->user->merchant_id,
                $validity,
                $confirmationUrl,
                $this->user->id,
                $validityType,
                $products,
                $customerId,
                $purchaseLimit
            );

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "create-quote",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => 'quote created',
                'quote-id' => $quote->slug
            ], 200);
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuote($id)
    {

        $quote = $this->app->getQuote($id, $this->user->merchant_id);


        if ($quote) {


            unset($quote['id']);
            unset($quote['updated_at']);
            unset($quote['created_at']);
            unset($quote['status']);
            unset($quote['merchant_id']);
            unset($quote['user_id']);

            unset($quote['slug']);

            $quote['quote_id'] = $id;
            $quote['plan_id'] = SAPlan::findorFail($quote->plan_id)->slug;

            if (($quote->client_id) > 0) {
                $quote['customer_id'] = User::findorFail($quote->client_id)->user_id;
            } else {
                $quote['customer_id'] = "none";
            }


            unset($quote['client_id']);
            unset($quote['prepopulate_check']);


            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "read-quote",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'quote' => $quote,
            ], 200);
        } else {

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "read-quote",
                json_encode(request()->all()),
                $responseTime,
                '404',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'error' => "no quote found",
            ], 404);
        }


    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateQuote($id)
    {


        $quoteName = request()->get('name');
        $planId = request()->get('plan_id');
        $validityType = request()->get('expiry_type');
        $validity = request()->get('expiry_value');
        $confirmationUrl = request()->get('confirmation_url');

        $customerId = request()->get('customer_id');
        $products = request()->get('products');
        $purchaseLimit = null;

        if ($validityType == 3) {
            $purchaseLimit = $validity;
        }


        if (strlen($customerId) > 1) {
            $customer = $this->api->GetUserFromUserIdForMerchant($customerId, $this->user->merchant_id);
            $customerId = $customer->id;
        }


        //      [{"id":"k9GvACVmc9EZnEG8m65K","quantity":1},{"id":"sdj910Bkkagk8d","quantity":1}],


        if (strlen($products) > 0) {
            $products = json_decode($products);

        }


        switch ($validityType) {
            case 0:
                $validity = null;
                break;
            case 1:
                if (!strtotime($validity)) {
                    return response()->json([
                        'error' => 'invalid expiry value - not valid date',
                    ], 422);
                }
                break;
            case 2:
                if (!($validity > 1)) {
                    return response()->json([
                        'error' => 'invalid expiry value',
                    ], 422);
                }
                break;
            case 3:
                if (!($validity > 1)) {
                    if (!($validity > 1)) {
                        return response()->json([
                            'error' => 'invalid expiry value',
                        ], 422);
                    }
                }
        }


        if (strlen($customerId) > 1) {
            $customer = $this->api->GetUserFromUserIdForMerchant($customerId, $this->user->merchant_id);
            $customerId = $customer->id;
        }

        if (strlen($quoteName) < 1 || strlen($validity) < 1 ||
            strlen($validityType) < 1
        ) {
            return response()->json([
                'error' => 'invalid or missing parameters'
            ], 400);
        } else {
            $quote = $this->app->updateQuote(
                $quoteName,
                $planId,
                $id,
                $this->user->merchant_id,
                $validity,
                $confirmationUrl,
                $validityType,
                $products,
                $customerId,
                $purchaseLimit = null
            );

            if ($quote) {

                $responseTime = microtime(true) - LARAVEL_START;

                $this->log->insertLog(
                    "update-quote",
                    json_encode(request()->all()),
                    $responseTime,
                    '200',
                    $this->user->id,
                    $this->user->merchant_id
                );


                return response()->json([
                    'status' => 'quote updated',
                ], 200);
            } else {

                $responseTime = microtime(true) - LARAVEL_START;

                $this->log->insertLog(
                    "update-quote",
                    json_encode(request()->all()),
                    $responseTime,
                    '404',
                    $this->user->id,
                    $this->user->merchant_id
                );

                return response()->json([
                    'error' => "no quote found",
                ], 404);
            }
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteQuote($id)
    {

        $status = $this->app->deleteQuote($id, $this->user->merchant_id);

        if ($status) {
            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "delete-quote",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => 'quote deleted',
            ], 200);
        } else {
            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "delete-quote",
                json_encode(request()->all()),
                $responseTime,
                '404',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'error' => "no quote found",
            ], 404);
        }


    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function allQuotes()
    {

        $quotes = $this->app->allQuotes($this->user->merchant_id);

        foreach ($quotes as $quote) {
            unset($quote['id']);
            unset($quote['updated_at']);
            unset($quote['created_at']);
            unset($quote['status']);
            unset($quote['merchant_id']);
            unset($quote['user_id']);

            unset($quote['slug']);

            $quote['quote_id'] = $quote['id'];
            $quote['plan_id'] = SAPlan::findorFail($quote->plan_id)->slug;

            if (($quote->client_id) > 0) {
                $quote['customer_id'] = User::findorFail($quote->client_id)->user_id;
            } else {
                $quote['customer_id'] = "none";
            }


            unset($quote['client_id']);
            unset($quote['prepopulate_check']);

        }

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "read-all-quotes",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'quotes' => $quotes,
        ], 200);
    }

    public function fetchQuoteForClient()
    {

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDynamicQuote()
    {

        //k9GvACVmc9EZnEG8m65K

        $planId = request()->get('plan_id');
        $price = request()->get('price');

        $plan = SAPlan::GetBySlug($planId)[0];
        $pricings = $plan->pricing->all();

        $plans = array();


        foreach ($pricings as $pricing) {
            if ($plan->first_payment > 0) {
                $firstPayment = $plan->first_payment;
            } else {
                $firstPayment = 8.34;
            }


            $increase = $pricing->price_change;
            $months = $pricing->instalments;


            if (request()->get('tax') > 0) {
                $newPrice = $price + request()->get('tax') * $price / 100;
            } else {
                $newPrice = $price;
            }

            $hike = 1 + $increase / 100;


            $planDetails = $this->bizpay->customPlans(
                $newPrice * $hike,
                $firstPayment, //in percent
                $months - 1
            );

            array_push($plans, $planDetails);
        }


        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "create-dynamic-quote",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );


        return response()->json([
            'plans' => $plans,
        ], 200);
    }

    public function getDynamicQuote()
    {

    }

    public function updateDynamicQuote()
    {

    }

    public function deleteDynamicQuote()
    {

    }

    public function allDynamicQuotes()
    {

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMerchantSettings()
    {

        $merchant = Merchant::findorFail($this->user->merchant_id);

        unset($merchant['id']);
        unset($merchant['created_at']);
        unset($merchant['updated_at']);
        unset($merchant['status']);
        unset($merchant['card_check']);
        unset($merchant['dd_check']);
        unset($merchant['merchant_id']);
        unset($merchant['merchant_category']);
        unset($merchant['direct_client']);
        unset($merchant['bizpay_credit']);


        if ($merchant['gateway'] == 1) {
            $merchant['gateway'] = "Stripe";
        }

        if ($merchant['gateway'] == 2) {
            $merchant['gateway'] = "GoCardless";
        }

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "read-merchant-settings",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'settings' => $merchant,
        ], 200);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveMerchantSettings()
    {

        $merchantWebsite = request()->get('merchant-website');
        $merchantLogo = request()->get('merchant-logo');
        $merchantName = request()->get('merchant-name');
        $merchantPhoneNumber = request()->get('merchant-phone-number');
        $merchantCompanyNo = request()->get('merchant-company-number');
        $merchantTaxNo = request()->get('merchant-tax-no');
        $merchantStaffNumber = request()->get('merchant-staff-number');
        $merchantIndustry = request()->get('merchant-industry');
        $merchantGateway = strtolower(request()->get('merchant-gateway'));

        if ($merchantGateway == "stripe") {
            $merchantGateway = 1;
        }

        if ($merchantGateway == "gocardless") {

            $merchantGateway = 2;

        }


        $this->app->saveSettings(
            $this->user->merchant_id,
            $merchantWebsite,
            $merchantLogo,
            $merchantGateway,
            $merchantName,
            $merchantPhoneNumber,
            $merchantCompanyNo,
            $merchantTaxNo,
            $merchantStaffNumber,
            $merchantIndustry
        );

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "post-merchant-settings",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );


        return response()->json([
            'status' => "merchant settings updated",
        ], 200);


    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function api()
    {
        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "api-limit-check",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'api-limit' => $this->user->api_limit,
            'api-usage' => $this->user->api_usage,
        ], 200);

    }

    /**
     *
     */
    public function maintenance()
    {
        exit(response()->json([
            "error" => "API is down for maintenance"
        ], 503));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function agreementPaymentSuccess($id)
    {

        $slug = $id;
        $agreement = $this->bizpay->getAgreement($slug);
        $amount = request()->get('amount');


        if (($agreement)) {

            $merchant = Merchant::findorFail($this->user->merchant_id);


            $merchantWebsite = $merchant->merchant_website;
            $merchantLogoFile = $merchant->merchant_logo;
            $merchantName = $merchant->merchant_name;
            $merchantEmail = $this->user->email;
            $agreementId = $slug;

            $date = Carbon::now()->toDateString();
            $customerDetails = User::findorFail($agreement->user_id);


            $currencyCode = $agreement->currency_code;
            $customerName = $customerDetails->name;
            $customerEmail = $customerDetails->email;


            $this->mail->paymentSucceeded($merchantWebsite,
                $customerName,
                $merchantLogoFile,
                $customerEmail,
                $agreementId,
                $merchantName,
                $merchantEmail,
                $amount,
                $currencyCode,
                $date
            );


            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "mail-payment-success",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => "message sent",
            ], 200);

        } else {
            return response()->json([
                'status' => "invalid agreement id"
            ], 400);
        }


    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function agreementPaymentFailed($id)
    {

        $slug = $id;
        $agreement = $this->bizpay->getAgreement($slug);
        $amount = request()->get('amount');


        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "mail-payment-failed",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );


        if (($agreement)) {

            $merchant = Merchant::findorFail($this->user->merchant_id);


            $merchantWebsite = $merchant->merchant_website;
            $merchantLogoFile = $merchant->merchant_logo;
            $merchantName = $merchant->merchant_name;
            $merchantEmail = $this->user->email;
            $agreementId = $slug;

            $date = Carbon::now()->toDateString();
            $customerDetails = User::findorFail($agreement->user_id);


            $currencyCode = $agreement->currency_code;
            $customerName = $customerDetails->name;
            $customerEmail = $customerDetails->email;


            $this->mail->paymentMissed($merchantWebsite,
                $customerName,
                $merchantLogoFile,
                $customerEmail,
                $agreementId,
                $merchantName,
                $merchantEmail,
                $amount,
                $currencyCode,
                $date
            );


            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "mail-payment-success",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => "message sent",
            ], 200);

        } else {
            return response()->json([
                'status' => "invalid agreement id"
            ], 400);
        }

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function agreementPaymentReminder($id)
    {

        $slug = $id;
        $agreement = $this->bizpay->getAgreement($slug);
        $amount = request()->get('amount');


        if ($agreement) {

            $merchant = Merchant::findorFail($this->user->merchant_id);


            $merchantWebsite = $merchant->merchant_website;
            $merchantLogoFile = $merchant->merchant_logo;
            $merchantName = $merchant->merchant_name;
            $merchantEmail = $this->user->email;
            $agreementId = $slug;

            $date = Carbon::now()->toDateString();
            $customerDetails = User::findorFail($agreement->user_id);


            $currencyCode = $agreement->currency_code;
            $customerName = $customerDetails->name;
            $customerEmail = $customerDetails->email;

            $this->mail->paymentReminder($customerName, $agreementId, $merchantName, $customerEmail, $amount, $currencyCode, $date);

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "mail-payment-reminder",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => "message sent",
            ], 200);
        } else {
            return response()->json([
                'status' => "invalid agreement id"
            ], 400);
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function agreementRenewed($id)
    {
        $slug = $id;
        $agreement = $this->bizpay->getAgreement($slug);

        $merchant = Merchant::findorFail($this->user->merchant_id);


        $merchantWebsite = $merchant->merchant_website;
        $merchantLogoFile = $merchant->merchant_logo;
        $merchantName = $merchant->merchant_name;
        $merchantEmail = $this->user->email;
        $agreementId = $slug;

        $date = Carbon::now()->toDateString();
        $customerDetails = User::findorFail($agreement->user_id);


        $currencyCode = $agreement->currency_code;
        $customerName = $customerDetails->name;
        $customerEmail = $customerDetails->email;


        if ($agreement) {

            $instalmentPeriod = $agreement->billing_period;  // TODO: change to string
            $instalments = $agreement->instalments;
            $agreementTotal = $agreement->first_payment + $agreement->recurring_payment * $agreement->instalments;

            $this->mail->agreementRenewed($customerEmail, $customerName, $agreementTotal, $instalmentPeriod, $instalments);

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "mail-agreement-renewed",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );


            return response()->json([
                'status' => "message sent",
            ], 200);
        } else {
            return response()->json([
                'status' => "invalid agreement id"
            ], 400);
        }


    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendQuote($id)
    {

        $customerEmail = request()->get('customer-email');
        $customerName = request()->get('customer-name');
        $quoteId = $id;

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "mail-quote",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );


        $this->mail->sendQuote($customerEmail, $customerName, $quoteId);

        return response()->json([
            'status' => "message sent",
        ], 200);

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function agreementCreated($id)
    {

        $slug = $id;
        $agreement = $this->bizpay->getAgreement($slug);

        $slug = $id;
        $agreement = $this->bizpay->getAgreement($slug);


        $merchant = Merchant::findorFail($this->user->merchant_id);


        $merchantWebsite = $merchant->merchant_website;
        $merchantLogoFile = $merchant->merchant_logo;
        $merchantName = $merchant->merchant_name;
        $merchantEmail = $this->user->email;
        $agreementId = $slug;

        $date = Carbon::now()->toDateString();
        $customerDetails = User::findorFail($agreement->user_id);


//        $customerEmail = request()->get('customer-email');
//        $merchantName = request()->get('merchant-name');
//        $customerName = request()->get('customer-name');
//        $agreementTotal = request()->get('agreement-total');
//        $instalmentPeriod = request()->get('instalment-period');
//        $instalments = request()->get('instalments');


        $currencyCode = $agreement->currency_code;
        $customerName = $customerDetails->name;
        $customerEmail = $customerDetails->email;


        if ($agreement) {

            $instalmentPeriod = $agreement->billing_period;  // TODO: change to string
            $instalments = $agreement->instalments;
            $agreementTotal = $agreement->first_payment + $agreement->recurring_payment * $agreement->instalments;


            $merchantEmail = $this->user->email;

            $this->mail->newAgreement($customerEmail, $merchantName, $customerName, $agreementTotal, $instalmentPeriod, $instalments);

            $this->mail->agreementCreatedClientEmail($merchantEmail, $merchantName, $customerEmail);

            $responseTime = microtime(true) - LARAVEL_START;
            $this->log->insertLog(
                "mail-agreement-created",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );

            return response()->json([
                'status' => "message sent",
            ], 200);
        } else {
            return response()->json([
                'status' => "invalid agreement id"
            ], 400);
        }

    }

    public function agreementCancelled($id)
    {
        $slug = $id;
        $agreement = $this->bizpay->getAgreement($slug);


        $merchant = Merchant::findorFail($this->user->merchant_id);


        $merchantWebsite = $merchant->merchant_website;
        $merchantLogoFile = $merchant->merchant_logo;
        $merchantName = $merchant->merchant_name;
        $merchantEmail = $this->user->email;
        $agreementId = $slug;

        $date = Carbon::now()->toDateString();
        $customerDetails = User::findorFail($agreement->user_id);


        $currencyCode = $agreement->currency_code;
        $customerName = $customerDetails->name;
        $customerEmail = $customerDetails->email;


        if ($agreement) {

            $instalmentPeriod = $agreement->billing_period;  // TODO: change to string
            $instalments = $agreement->instalments;
            $agreementTotal = $agreement->first_payment + $agreement->recurring_payment * $agreement->instalments;

            $this->mail->agreementCancelled($customerEmail, $customerName, $agreementTotal, $instalmentPeriod, $instalments);

            $responseTime = microtime(true) - LARAVEL_START;

            $this->log->insertLog(
                "mail-agreement-renewed",
                json_encode(request()->all()),
                $responseTime,
                '200',
                $this->user->id,
                $this->user->merchant_id
            );


            return response()->json([
                'status' => "message sent",
            ], 200);
        } else {
            return response()->json([
                'status' => "invalid agreement id"
            ], 400);
        }


    }

    /**
     * Get all agreements for a merchant
     */
    public function getAllAgreementsForMerchant()
    {
       $agreements = $this->bizpay->GetAllAgreementsForMerchant($this->user->merchant_id);

       foreach ($agreements as $agreement){
           unset($agreement["created_at"]);
           unset($agreement["updated_at"]);
   //        $agreement["customer_id"] = User::findorFail($agreement["user_id"])->user_id; // local check
           unset($agreement["id"]);
           unset($agreement["merchant_id"]);
           unset($agreement["user_id"]);

           //   unset($plan["status"]);
//
           if($agreement["status"]=="1"){
               $agreement["status"] ="active";
           }

           if($agreement["cancelled"]=="0"){
               $agreement["status"] ="cancelled";
           }

           if($agreement["cancelled"]=="-1"){
               $agreement["status"] ="payment failed";
           }

           if($agreement["cancelled"]=="-2"){
               $agreement["status"] ="no payment info";
           }

           $agreement["plan_id"] = $agreement["slug"];
           unset($agreement["slug"]);
           $agreement["agreement_id"] = $agreement["merchant_slug"];
           unset($agreement["merchant_slug"]);
           unset($agreement["bizpay_order_id"]);


           $agreement["can_cancel"] = $agreement["can_cancel"] > 0 ? true : false;
           $agreement["refund_check"] = $agreement["refund_check"] > 0 ? true : false;
           $agreement["payment_info_required"] = $agreement["payment_info_required"] > 0 ? true : false;
           $agreement["renewal_check"] = $agreement["renewal"] > 0 ? true : false;
           $agreement["different_first_payment"] = $agreement["different_first_payment"] > 0 ? true : false;
           $agreement["structure"] = $agreement["structure"] > 1 ? "instalment" : "single";


           $billingPeriod = array(
               "1" => "daily",
               "2" => "weekly",
               "3" => "monthly",
               "4" => "quarterly",
               "5" => "biyearly",
               "6" => "yearly",
               "7" => "biweekly",
           );

           $agreement["billing_period"] = $billingPeriod[$agreement["billing_period"]];


           unset($agreement["renewal"]);
       }

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "read-all-agreements(m)",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'agreements' => $agreements,
        ], 200);
    }

    /**
     * Create a new agreement based on a plan and dynamic quote (OR simply using quote)
     */
    public function createAgreement()
    {

       $planId = request()->get('plan_id');
        $duration = request()->get('selected_duration');


        $plan = SAPlan::GetPlan( $planId, $this->user->merchant_id)[0];




        $currencyCode = request()->get('currency_code');
        $amount = request()->get('amount');
        $gatewayId = strtolower(request()->get('gateway')) ;
        $quoteId = request()->get('quote_id');


        if($gatewayId=="stripe"){
            $gatewayId=1;
        }

        if($gatewayId=="gocardless"){
            $gatewayId=2;
        }


        $pricings = $plan->pricing->all();

        $planDetails = array();


        foreach ($pricings as $pricing) {

            if($pricing->instalments==$duration) {

                if ($plan->first_payment > 0) {
                    $firstPayment = $plan->first_payment;
                } else {
                    $firstPayment = 8.34;
                }


                $increase = $pricing->price_change;
                $months = $pricing->instalments;


                if (request()->get('tax') > 0) {
                    $newPrice = $amount + request()->get('tax') * $amount / 100;
                } else {
                    $newPrice = $amount;
                }

                $hike = 1 + $increase / 100;


                $planDetails = $this->bizpay->customPlans(
                    $newPrice * $hike,
                    $firstPayment, //in percent
                    $months - 1
                );
            }

        }



        $dynamicProducts = request()->get('products');

      //  [{"price":100.00, "tax":"20.00", "currency":"GBP", "details":""}]

        if (strlen($dynamicProducts) > 2) {
            $dynamicProducts = json_decode($dynamicProducts);
        }



        $paymentCheck = request()->get('payment_check');
        $customerId = request()->get('customer_id');

        if(strlen($customerId)<5 || strlen($customerId)<2 || !is_numeric($gatewayId) ){
            return response()->json([
                'error' => "invalid or missing parameters",
            ], 400);
        }



       $agreement = $this->bizpay-> createAgreement(
            $plan,
            $currencyCode,
            $duration,
            $gatewayId,
            $dynamicProducts,
            $paymentCheck,
           $planDetails['first_instalment'],
           $planDetails['recurring_instalment_amount'],
            $quoteId,
           $customerId,
           $this->user->merchant_id
        );


        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "create-agreement",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'agreement_id' => $agreement->merchant_slug,
        ], 200);

    }

    public function getAgreement($ref)
    {

       $agreement = SAAgreement::GetAAgreementForMerchant($ref)[0];

        unset($agreement["created_at"]);
        unset($agreement["updated_at"]);
        $agreement["customer_id"] = $agreement["user_id_string"];
        unset($agreement["id"]);
        unset($agreement["merchant_id"]);
        unset($agreement["user_id"]);
        unset($agreement["user_id_string"]);

        $agreement["status"] = (int)$agreement["status"];


        if($agreement["status"]===1){
            $agreement["status"] ="active";
        }

        if($agreement["cancelled"]===0){
            $agreement["status"] ="cancelled";
        }

        if($agreement["cancelled"]===-2){
            $agreement["status"] ="payment failed";
        }

        if($agreement["cancelled"]===-1){
            $agreement["status"] ="no payment info";
        }

        $agreement["plan_id"] = $agreement["slug"];
        unset($agreement["slug"]);
        $agreement["agreement_id"] = $agreement["merchant_slug"];
        unset($agreement["merchant_slug"]);
        unset($agreement["bizpay_order_id"]);


        $agreement["can_cancel"] = $agreement["can_cancel"] > 0 ? true : false;
        $agreement["refund_check"] = $agreement["refund_check"] > 0 ? true : false;
        $agreement["payment_info_required"] = $agreement["payment_info_required"] > 0 ? true : false;
        $agreement["renewal_check"] = $agreement["renewal"] > 0 ? true : false;
        $agreement["different_first_payment"] = $agreement["different_first_payment"] > 0 ? true : false;
        $agreement["structure"] = $agreement["structure"] > 1 ? "instalment" : "single";


        $billingPeriod = array(
            "1" => "daily",
            "2" => "weekly",
            "3" => "monthly",
            "4" => "quarterly",
            "5" => "biyearly",
            "6" => "yearly",
            "7" => "biweekly",
        );

        $agreement["billing_period"] = $billingPeriod[$agreement["billing_period"]];


        unset($agreement["renewal"]);


        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "read-agreement(m)",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'agreement_id' => $agreement,
        ], 200);

    }

    public function cancelAgreement($ref)
    {

       $agreement = $this->bizpay->cancelAgreement($ref);



       if($agreement){

           $responseTime = microtime(true) - LARAVEL_START;

           $this->log->insertLog(
               "cancel-agreement",
               json_encode(request()->all()),
               $responseTime,
               '404',
               $this->user->id,
               $this->user->merchant_id
           );

           return response()->json([
               'status' => "agreement cancelled",
           ], 200);



       } else {
           $responseTime = microtime(true) - LARAVEL_START;

           $this->log->insertLog(
               "cancel-agreement",
               json_encode(request()->all()),
               $responseTime,
               '404',
               $this->user->id,
               $this->user->merchant_id
           );
           return response()->json([
               'error' => "agreement not found",
           ], 404);
       }



    }

    /**
     * @param $ref
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFailedPaymentForAgreement($ref)
    {

      $payments = $this->bizpay->failedPayments($ref,$this->user->merchant_id);

      if(count($payments)>0){
          return response()->json([
              'payments' => $payments,
          ], 200);
      } else {

          return response()->json([
              'status' => "no failed payments linked to this agreement",
          ], 200);
      }

    }


    /**
     * @param $ref
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentInformation($ref)
    {
        $payments = $this->bizpay->getPaymentInformation($ref, $this->user->merchant_id);
        foreach ($payments as $payment) {
            unset($payment['id']);
            unset($payment['created_at']);
            unset($payment['updated_at']);
            unset($payment['status_text']);

            unset($payment['merchant_id']);
            unset($payment['added_by']);
            unset($payment['user_id']);
            unset($payment['s_a_agreement_id']);
            unset($payment['order_id']);
            unset($payment['description']);
            unset($payment['renewal_check']);
            unset($payment['tax']);

            if ($payment['payment_gateway'] == "1") {
                $payment['payment_gateway'] = "stripe";
            }

            if ($payment['payment_gateway'] == "2") {
                $payment['payment_gateway'] = "gocardless";
            }


            if ($payment['status'] == "1") {
                $payment['status'] = "not processed";
            }

            if ($payment['status'] == "0") {
                $payment['status'] = "paid";
            }

            if ($payment['status'] == "-1") {
                $payment['status'] = "failed";
            }


            if ($payment['order_type'] == 1) {
                $payment['order_type'] = "one-off";
            }

            if ($payment['order_type'] == 2) {
                $payment['order_type'] = "recurring";
            }

        }

        $responseTime = microtime(true) - LARAVEL_START;

        $this->log->insertLog(
            "get-payments",
            json_encode(request()->all()),
            $responseTime,
            '200',
            $this->user->id,
            $this->user->merchant_id
        );

        return response()->json([
            'payments' => ($payments)
        ], 200);
    }



}
