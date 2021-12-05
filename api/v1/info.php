<?php
$is_manual = isset($_SERVER['PATH_INFO']);
$path = $_SERVER['PATH_INFO'] ?? time();
$path = trim($path, '/');

if (!is_numeric($path)) {
    http_response_code(400);
    return;
}

if ($is_manual && $path < time()) {
    header('Cache-Control: public');
} else {
    header('Cache-Control: no-store');
}

if ($path < 0) {
    http_response_code(404);
    return;
} else if ($path > time()) {
    http_response_code(404);
    return;
}

require_once __DIR__ . '/../_internal/init.php';

$data = pretty(get_from_kmoni($path));

if ($data !== null) {
    header('Content-Type: application/json; charset=utf-8');
    print(json_encode($data));
} else {
    http_response_code(204);
}
