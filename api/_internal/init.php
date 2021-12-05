<?php

const TZSTR_ASIA_TOKYO = 'Asia/Tokyo';
const EEW_DATETIME_FORMAT = 'YmdHis';

function get_from_kmoni($unixtime): array
{
    date_default_timezone_set(TZSTR_ASIA_TOKYO);
    $kmontime = date(EEW_DATETIME_FORMAT, $unixtime);

    $api_endpoint = 'http://www.kmoni.bosai.go.jp/webservice/hypo/eew/' . $kmontime . '.json';

    $raw_json = file_get_contents($api_endpoint);
    $json = json_decode($raw_json, true);

    return $json;
}

function japanese_intensity_to_int($int): int
{
    switch ($int) {
        case '0':
        case '1':
        case '2':
        case '3':
        case '4':
            return intval($int);

        case '5弱':
            return 5;
        case '5強':
            return 6;

        case '6弱':
            return 7;
        case '6強':
            return 8;

        case '7':
            return 9;
    }

    return -1;
}

function pretty($kmon)
{
    if ($kmon['result']['message'] === 'データがありません') {
        return null;
    }

    $return_data = array(
        'time' => date_create_from_format(EEW_DATETIME_FORMAT, $kmon['request_time'], new DateTimeZone(TZSTR_ASIA_TOKYO))->getTimestamp(),
        'region' => $kmon['region_name'],
        'longitude' => floatval($kmon['longitude']),
        'latitude' => floatval($kmon['latitude']),
        'depth' => str_replace('km', '', $kmon['depth']),
        'japanese_intensity' => japanese_intensity_to_int($kmon['calcintensity']),
        'report_num' => intval($kmon['report_num']),
        'alert_type' => $kmon['alertflg'],
    );

    if ($kmon['magunitude'] !== NAN) {
        $return_data['magunitude'] = floatval($kmon['magunitude']);
    }

    return $return_data;
}
