<?php
// Wajib: Memuat file konfigurasi database dan kelas-kelas inti
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../includes/Database.php';
include_once __DIR__ . '/../../models/VaultItem.php';
// Anda juga memerlukan kelas User jika ingin melakukan validasi token/user
// include_once __DIR__ . '/../../models/User.php'; 

// HANYA terima metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405 Method Not Allowed
    echo json_encode(array("message" => "Metode request tidak diizinkan."));
    exit();
}

// Mendapatkan koneksi database
$database = new Database();
$db = $database->getConnection();

// Menginisialisasi objek VaultItem
$item = new VaultItem($db);

// Mendapatkan data POST dari body request JSON
$data = json_decode(file_get_contents("php://input"));

// Memastikan data esensial tidak kosong
if(
    !empty($data->user_id) && // Dalam aplikasi nyata, ini dari Token JWT
    !empty($data->title) &&
    !empty($data->username) &&
    !empty($data->password_encrypted) // Data sudah dienkripsi di sisi klien
){
    // Menetapkan nilai properti objek VaultItem
    $item->user_id = $data->user_id; 
    $item->title = $data->title;
    $item->username = $data->username;
    $item->password_encrypted = $data->password_encrypted;
    
    // Properti opsional
    $item->url = isset($data->url) ? $data->url : '';
    $item->notes = isset($data->notes) ? $data->notes : '';

    // Mencoba membuat item brankas
    if($item->create()){
        // Respon Berhasil
        http_response_code(201); // 201 Created
        echo json_encode(
            array("message" => "Item brankas berhasil dibuat.")
        );
    } else {
        // Respon Gagal (jika ada masalah database)
        http_response_code(503); // 503 Service Unavailable
        echo json_encode(array("message" => "Gagal membuat item brankas. Layanan tidak tersedia."));
    }
} else {
    // Respon Gagal (jika ada data yang hilang)
    http_response_code(400); // 400 Bad Request
    echo json_encode(array("message" => "Tidak dapat membuat item brankas. Data tidak lengkap."));
}

?>