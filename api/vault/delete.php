<?php
// Wajib: Memuat file konfigurasi database dan kelas-kelas inti
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../includes/Database.php';
include_once __DIR__ . '/../../models/VaultItem.php';

// HANYA terima metode POST atau DELETE
// Kita gunakan DELETE sebagai metode RESTful yang tepat
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405); // 405 Method Not Allowed
    echo json_encode(array("message" => "Metode request tidak diizinkan."));
    exit();
}

// Mendapatkan koneksi database
$database = new Database();
$db = $database->getConnection();

// Menginisialisasi objek VaultItem
$item = new VaultItem($db);

// Mendapatkan data POST/DELETE dari body request JSON
$data = json_decode(file_get_contents("php://input"));

// Memastikan data esensial tidak kosong
if(
    !empty($data->id) &&             // ID Item yang akan dihapus
    !empty($data->user_id)           // ID Pengguna pemilik item
){
    // Menetapkan nilai properti objek VaultItem
    $item->id = $data->id;
    $item->user_id = $data->user_id; // Penting untuk verifikasi kepemilikan

    // Mencoba menghapus item brankas
    if($item->delete()){
        // Respon Berhasil
        http_response_code(200); // 200 OK
        echo json_encode(
            array("message" => "Item brankas berhasil dihapus.")
        );
    } else {
        // Respon Gagal (Item tidak ditemukan atau user_id tidak cocok)
        http_response_code(404); // 404 Not Found (atau 401 Unauthorized)
        echo json_encode(array("message" => "Gagal menghapus item brankas. Item tidak ditemukan atau Anda tidak memiliki izin."));
    }
} else {
    // Respon Gagal (jika ada data yang hilang)
    http_response_code(400); // 400 Bad Request
    echo json_encode(array("message" => "Tidak dapat menghapus item brankas. ID item atau ID pengguna hilang."));
}

?>