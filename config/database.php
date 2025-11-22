<?php

// Konfigurasi Database
define('DB_HOST', 'localhost'); // Ganti jika host database Anda berbeda
define('DB_NAME', 'password_manager_db'); // Ganti dengan nama database yang Anda buat
define('DB_USER', 'root'); // Ganti dengan username database Anda
define('DB_PASS', ''); // Ganti dengan password database Anda
define('DB_CHARSET', 'utf8mb4');

// Anda juga bisa menentukan header Content-Type untuk API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

?>