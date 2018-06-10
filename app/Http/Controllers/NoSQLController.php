<?php

namespace App\Http\Controllers;

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\Sdk;
use Illuminate\Http\Request;

class NoSQLController extends Controller
{


    private function createBizPayTable()
    {

        date_default_timezone_set('UTC');

        $sdk = new Sdk([
            'endpoint'   => 'https://dynamodb.eu-west-2.amazonaws.com',
            'region'   => 'eu-west-2',
            'version'  => 'latest',
            'credentials' => [
                'key'    => 'AKIAIJAKHJY2BIRE3KYA',
                'secret' => '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',
            ],
        ]);

        $tableName = 'Bizpay';
        $dynamodb = $sdk->createDynamoDb();

        $params = [
            'TableName' => $tableName,
            'KeySchema' => [
                [
                    'AttributeName' => 'order_id',
                    'KeyType' => 'HASH'
                ],
                [
                    'AttributeName' => 'details',
                    'KeyType' => 'RANGE'
                ]

            ],
            'AttributeDefinitions' => [
                [
                    'AttributeName' => 'order_id',
                    'AttributeType' => 'N'
                ],
                [
                    'AttributeName' => 'details',
                    'AttributeType' => 'S'
                ]

            ],
            'ProvisionedThroughput' => [
                'ReadCapacityUnits' => 10,
                'WriteCapacityUnits' => 10
            ]
        ];

        try {
            $result = $dynamodb->createTable($params);
            echo 'Created table.  Status: ' .
                $result['TableDescription']['TableStatus'] ."\n";

        } catch (DynamoDbException $e) {
            echo "Unable to create table:\n";
            echo $e->getMessage() . "\n";
        }


        $details=array();
        $details['invoice_no'] = "39872832";
        $details['user_id'] = "1";
        $details['merchant_id'] = "1";
        $details['price'] = "100";
        $details['tax'] = "20";
        $details['currency'] = "USD";

        $detailsString = json_encode($details);


        try {
            $result = $dynamodb->putItem(array(
                'TableName' => $tableName,
                'Item' => array(
                    'order_id'      => array('N' => '1201'),
                    'details' => array('S' => $detailsString)
                )
            ));
            echo "Added item";

        } catch (DynamoDbException $e) {
            echo "Unable to add item:\n";
            echo $e->getMessage() . "\n";
        }

    }



}
