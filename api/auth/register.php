<?php
// Wajib: Memuat file konfigurasi database dan kelas-kelas inti
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../includes/Database.php';
include_once __DIR__ . '/../../models/User.php';

// Mendapatkan koneksi database
$database = new Database();
$db = $database->getConnection();

// Menginisialisasi objek User
$user = new User($db);

// Mendapatkan data POST
// API biasanya menerima data mentah (raw data) dalam format JSON
$data = json_decode(file_get_contents("php://input"));

// Memastikan data tidak kosong
if(
    !empty($data->name) &&
    !empty($data->email) &&
    !empty($data->password)
){
    // Menetapkan nilai properti objek User dari data input
    $user->name = $data->name;
    $user->email = $data->email;
    $user->password = $data->password; // Password belum di-hash di sini, Model User yang akan menghash-nya

    // Mencoba mendaftarkan pengguna
    if($user->register()){
        // Respon Berhasil
        http_response_code(201); // 201 Created
        echo json_encode(
            array("message" => "Pengguna berhasil didaftarkan.", "user_id" => $user->id)
        );
    } else {
        // Respon Gagal (jika ada masalah database atau email sudah terdaftar)
        http_response_code(400); // 400 Bad Request
        echo json_encode(array("message" => "Gagal mendaftarkan pengguna. Email mungkin sudah digunakan."));
    }
} else {
    // Respon Gagal (jika ada data yang hilang)
    http_response_code(400); // 400 Bad Request
    echo json_encode(array("message" => "Tidak dapat mendaftarkan pengguna. Data tidak lengkap (nama, email, atau password hilang)."));
}

?>