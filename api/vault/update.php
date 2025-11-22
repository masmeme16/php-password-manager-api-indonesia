<?php
// Wajib: Memuat file konfigurasi database dan kelas-kelas inti
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../includes/Database.php';
include_once __DIR__ . '/../../models/VaultItem.php';

// HANYA terima metode POST atau PUT
// Kita gunakan POST/PUT untuk mengirim data pembaruan lengkap
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405); // 405 Method Not Allowed
    echo json_encode(array("message" => "Metode request tidak diizinkan."));
    exit();
}

// Mendapatkan koneksi database
$database = new Database();
$db = $database->getConnection();

// Menginisialisasi objek VaultItem
$item = new VaultItem($db);

// Mendapatkan data POST/PUT dari body request JSON
$data = json_decode(file_get_contents("php://input"));

// Memastikan data esensial tidak kosong
if(
    !empty($data->id) &&             // ID Item yang akan diupdate
    !empty($data->user_id) &&        // ID Pengguna pemilik item
    !empty($data->title) &&
    !empty($data->username) &&
    !empty($data->password_encrypted)
){
    // Menetapkan nilai properti objek VaultItem
    $item->id = $data->id;
    $item->user_id = $data->user_id; // Penting untuk verifikasi kepemilikan
    $item->title = $data->title;
    $item->username = $data->username;
    $item->password_encrypted = $data->password_encrypted;
    
    // Properti opsional
    $item->url = isset($data->url) ? $data->url : '';
    $item->notes = isset($data->notes) ? $data->notes : '';

    // Mencoba memperbarui item brankas
    if($item->update()){
        // Respon Berhasil
        http_response_code(200); // 200 OK
        echo json_encode(
            array("message" => "Item brankas berhasil diperbarui.")
        );
    } else {
        // Respon Gagal (Item tidak ditemukan atau user_id tidak cocok)
        http_response_code(404); // 404 Not Found (atau 401 Unauthorized jika karena user_id)
        echo json_encode(array("message" => "Gagal memperbarui item brankas. Item tidak ditemukan atau Anda tidak memiliki izin."));
    }
} else {
    // Respon Gagal (jika ada data yang hilang)
    http_response_code(400); // 400 Bad Request
    echo json_encode(array("message" => "Tidak dapat memperbarui item brankas. Data tidak lengkap."));
}

?>