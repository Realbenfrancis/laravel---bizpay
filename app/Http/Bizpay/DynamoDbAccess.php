<?php

namespace App\Http\Bizpay;

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Aws\Sdk;

/**
 *
 * Access DynamoDB
 * Operation currently allowed: Add record, fetch record, delete record
 *
 * Class DynamoDbAccess
 * @package App\Http\Bizpay
 */
class DynamoDbAccess{

    private $sdk;
    private $tableName = 'bizpay';


    public static function instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new self();
        }
        return $inst;
    }

    /**
     * DynamoDbAccess constructor.
     *
     * Change the values here to update the settings
     * This can be moved to env file
     *
     */
    public function __construct()
    {
        date_default_timezone_set('UTC');

        $this->sdk = new Sdk([
            'endpoint'   => 'https://dynamodb.eu-west-2.amazonaws.com',
            'region'   => 'eu-west-2',
            'version'  => 'latest',
            'credentials' => [
                'key'    => 'AKIAIXCSN5DQRMM365DA',
                'secret' => '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',
            ],
        ]);
    }

    /**
     * Only for testing!!
     * DO NOT CALL IN PRODUCTION
     */
    public function createDb()
    {

    }


    /**
     * Data order details to db
     *
     * @param $data
     */
    public function addData($orderId,$data)
    {
        $detailsString = json_encode($data);
        $dynamodb = $this->sdk->createDynamoDb();
        $message="";

        try {
            $result = $dynamodb->putItem(array(
                'TableName' => $this->tableName,
                'Item' => array(
                    'order_id'      => array('N' => "$orderId"),
                    'details' => array('S' => $detailsString)
                )
            ));

            $message= true;

        } catch (DynamoDbException $e) {
            $message=$e->getMessage();
        }

    }

    /**
     * Get record for a given order id
     *
     * @param $orderId
     * @return mixed|string
     */
    public function get($orderId)
    {
        $dynamodb = $this->sdk->createDynamoDb();
        $marshaler = new Marshaler();

        $key = $marshaler->marshalJson('
            {
                "order_id": '.$orderId.'
              
            }
        ');

        $params = [
            'TableName' => $this->tableName,
            'Key' => $key
        ];

        try {
            $result = $dynamodb->getItem($params);
            return $result["Item"];

        } catch (DynamoDbException $e) {
            return "Read Error";
        }
    }

    public function delete($orderId)
    {

    }
}