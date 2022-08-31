<?php

require_once __DIR__ . '/vendor/autoload.php';

use Aws\Sdk;

require('connect_to_dynamodb.php');

function get_city_list_from_csv($city_list)
{
    $city_code_csv = fopen('cityCodeList.csv', 'r');

    //１行目は使いません
    $head = fgetcsv($city_code_csv);

    while($city_code_line = fgetcsv($city_code_csv)) 
    {
        $city_list[$city_code_line[0]]['city_code'] = $city_code_line[1];
    }
    fclose($city_code_csv);

    return $city_list;
}

function get_city_info($city_list)
{
    $url_for_WEATHER = 'https://weather.tsukumijima.net/api/forecast';
    
    foreach($city_list as $city_name => $city)
    {
        $url_for_curl = $url_for_WEATHER . '/city/' . $city['city_code'];
        $curl_for_WEATHER = curl_init($url_for_curl);
        curl_setopt($curl_for_WEATHER, CURLOPT_RETURNTRANSFER, true);

        //jsonを取得して配列に変換
        $result_json = curl_exec($curl_for_WEATHER);
        curl_close($curl_for_WEATHER);
        $result_array = json_decode($result_json, true);

        //辞書型のリストに格納
        $city_list[$city_name]['weather'] = $result_array['forecasts'][0]['detail']['weather'];
        $city_list[$city_name]['location'] = $result_array['location'];
    }

    return $city_list;
}

function get_pref_code()
{
    $url_for_RESAS = 'https://opendata.resas-portal.go.jp/api/v1/prefectures';
    
    //APIKEYをCSVから取得
    $api_key_csv = fopen('api-key.csv', 'r');
    $api_key = fgetcsv($api_key_csv);
    $headers_for_RESAS = ['X-API-KEY: ' . $api_key[0]];

    $curl_for_RESAS = curl_init($url_for_RESAS);
    curl_setopt($curl_for_RESAS,CURLOPT_HTTPHEADER, $headers_for_RESAS);
    curl_setopt($curl_for_RESAS, CURLOPT_RETURNTRANSFER, true);

    //jsonを取得して配列に変換
    $result_json = curl_exec($curl_for_RESAS);
    curl_close($curl_for_RESAS);
    $result_array = json_decode($result_json, true);
    $prefectures_list = $result_array['result'];
    
    //扱いやすい辞書型に変換
    $prefecture_dict = [];
    for($i = 0; $i < count($prefectures_list); $i++)
    {
        $prefecture_name = $prefectures_list[$i]['prefName'];
        $prefecture_code = $prefectures_list[$i]['prefCode'];
        $prefecture_dict[$prefecture_name] = $prefecture_code;
    }
    
    return $prefecture_dict;
}
