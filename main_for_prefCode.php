<?php

require('functions.php');

function main()
{
    $city_list = scan_table();
    $pref_code_list = get_pref_code();
    $upload_list = [];
    foreach($city_list as $city)
    {
        $city_name = $city['name']['S'];
        $city_code = $city['id']['N'];
        $pref_code = $pref_code_list[$city['prefecture']['S']];
        $upload_list[$city_name] = [
            'city_code' => $city_code,
            'pref_code' => $pref_code
        ];
    }
    print_r($upload_list);
    add_prefCode_to_db($upload_list);
    $result = scan_table();
    print_r($result);

}

main();