<?php
// 1. Dapatkan Request URI
$request = $_SERVER['REQUEST_URI'];
// Hapus prefix folder dan query string
$request = trim(parse_url($request, PHP_URL_PATH), '/'); 

// Anggap struktur kita adalah: api/auth/login
$segments = explode('/', $request);

// Asumsi: Request pertama adalah api/auth/login
if ($segments[0] === 'api') {
    $endpoint_path = implode('/', array_slice($segments, 1)) . '.php'; // auth/login.php

    // Jika file endpoint (misalnya api/auth/login.php) ada
    if (file_exists('api/' . $endpoint_path)) {
        require_once 'api/' . $endpoint_path;
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Endpoint tidak ditemukan']);
    }

} else {
    // Tangani request non-API (misalnya home page)
    http_response_code(404);
    echo json_encode(['message' => 'Resource tidak ditemukan']);
}