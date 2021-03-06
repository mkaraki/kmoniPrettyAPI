<?php
require_once __DIR__ . '/../vendor/autoload.php';

const TZSTR_ASIA_TOKYO = 'Asia/Tokyo';
const EEW_DATETIME_FORMAT = 'YmdHis';

DB::$dbName = 'eew';
DB::$encoding = 'utf8';

require_once __DIR__ . '/db_cred.php';

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

function kmoni_time_to_unixtime($strtime): int
{
    return date_create_from_format(EEW_DATETIME_FORMAT, $strtime, new DateTimeZone(TZSTR_ASIA_TOKYO))->getTimestamp();
}

function pretty($kmon): ?array
{
    if ($kmon['result']['message'] === 'データがありません') {
        return null;
    }

    $return_data = array(
        'time' => kmoni_time_to_unixtime($kmon['request_time']),
        'region' => $kmon['region_name'],
        'longitude' => floatval($kmon['longitude']),
        'latitude' => floatval($kmon['latitude']),
        'depth' => intval(str_replace('km', '', $kmon['depth'])),
        'japanese_intensity' => japanese_intensity_to_int($kmon['calcintensity']),
        'report_num' => intval($kmon['report_num']),
        'alert_type' => $kmon['alertflg'],
        'origin_time' => kmoni_time_to_unixtime($kmon['origin_time']),
    );

    if ($kmon['magunitude'] !== NAN) {
        $return_data['magunitude'] = floatval($kmon['magunitude']);
    }

    if ($kmon['is_final'] === 'true') {
        $return_data['final'] = true;
    }

    return $return_data;
}

function get_data_from_db($unixtime): array|bool|null
{
    $data = DB::queryFirstRow('SELECT * FROM logs WHERE time = %i', $unixtime);
    if ($data === null) {
        return false;
    }

    if ($data['available'] === '0') {
        return null;
    }

    unset($data['available']);

    $return_data = array(
        'time' => intval($data['time']),
        'region' => $data['region'],
        'longitude' => floatval($data['longitude']),
        'latitude' => floatval($data['latitude']),
        'depth' => intval($data['depth']),
        'japanese_intensity' => intval($data['japanese_intensity']),
        'report_num' => intval($data['report_num']),
        'alert_type' => $data['alert_type'],
        'origin_time' => intval($data['origin_time']),
    );

    if ($data['magunitude'] !== null) {
        $return_data['magunitude'] = floatval($data['magunitude']);
    }

    if ($data['final'] ?? null === '1') {
        $return_data['final'] = true;
    }

    return $return_data;
}

function set_data_to_db($unixtime, $data): void
{
    if ($data === null) {
        DB::insert('logs', [
            'time' => $unixtime,
            'available' => false,
        ]);
        return;
    }

    DB::insert('logs', $data);
}
