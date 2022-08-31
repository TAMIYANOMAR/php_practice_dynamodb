<?php

require_once __DIR__ . '/vendor/autoload.php';

use Aws\Sdk;
use Aws\DynamoDb\Marshaler;


function create_db_client()
{
    $sdk = new Sdk([
        'endpoint' => 'dynamodb:8000',
        'region' => 'ap-northeast-1',
        'version' => 'latest',
        'aws_access_key_id' => 'fake',
        'aws_secret_access_key' => 'fake'
    ]);
    $dynamoDb = $sdk->createDynamoDb();
    return $dynamoDb;
}

function create_db_table()
{
    $params = 
    [
        'TableName' => 'Cities',
        'KeySchema' => 
        [
            [
                'AttributeName' => 'id',
                'KeyType' => 'HASH'
            ],
            [
                'AttributeName' => 'name',
                'KeyType' => 'RANGE'
            ],
        ],
        'AttributeDefinitions' => 
        [
            [
                'AttributeName' => 'id',
                'AttributeType' => 'N'
            ],
            [
                'AttributeName' => 'name',
                'AttributeType' => 'S'
            ],
        ],
        'ProvisionedThroughput' => 
        [
            'ReadCapacityUnits' => 10,
            'WriteCapacityUnits' => 10
        ]
      ];
    $dynamo_client = create_db_client();
    $result = $dynamo_client->createTable($params);
    var_dump($result);
}

function scan_table()
{
    $dynamo_client = create_db_client();
    $scan = $dynamo_client->scan([
        'TableName' => 'Cities'
    ]);

    $marshaler = new Marshaler();

    return $scan['Items'];
}

function intert_to_db($city_list)
{
    $dynamo_client = create_db_client();
    foreach ($city_list as $city_name => $city) 
    {
        $params = 
        [
            'TableName' => 'Cities',
            'Item' => 
            [
                'id' => 
                [
                    'N' => $city['city_code']
                ],
                'name' => 
                [
                    'S' => $city_name
                ],
            ]
        ];
        $result = $dynamo_client->putItem($params);
        //var_dump($result);
    }
}

function update_db($city_list)
{
    $dynamo_client = create_db_client();
    foreach ($city_list as $city_name => $city) 
    {
        $params = 
        [
            'TableName' => 'Cities',
            'Key' => 
            [
                'id' => 
                [
                    'N' => $city['city_code']
                ],
                'name' => 
                [
                    'S' => $city_name
                ]

            ],
            'AttributeUpdates' => 
            [
                'weather' => 
                [
                    'Action' => 'PUT',
                    'Value' => 
                    [
                        'S' => $city['weather']
                    ]
                ],
                'prefecture' => 
                [
                    'Action' => 'PUT',
                    'Value' => 
                    [
                        'S' => $city['location']['prefecture']
                    ]
                ]
            ]
        ];
        $result = $dynamo_client->updateItem($params);
        //var_dump($result);
    }
}

function add_prefCode_to_db($city_list)
{
    $dynamo_client = create_db_client();
    foreach ($city_list as $city_name => $city) 
    {
        $params = 
        [
            'TableName' => 'Cities',
            'Key' => 
            [
                'id' => 
                [
                    'N' => $city['city_code']
                ],
                'name' => 
                [
                    'S' => $city_name
                ]

            ],
            'AttributeUpdates' => 
            [
                'prefecture_code' => 
                [
                    'Action' => 'PUT',
                    'Value' => 
                    [
                        'N' => $city['pref_code']
                    ]
                ]
            ]
        ];
        $result = $dynamo_client->updateItem($params);
        //var_dump($result);
    }
}