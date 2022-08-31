<?php

require_once __DIR__ . '/vendor/autoload.php';

require('functions.php');
//require('connect_to_dynamodb.php');

function main()
{
    $city_list = [];
    $city_list = get_city_list_from_csv($city_list);
    $city_list = get_city_info($city_list);

    create_db_table();
    intert_to_db($city_list);
    update_db($city_list);
    $scan_result = scan_table();
    print_r($scan_result);

}

main();