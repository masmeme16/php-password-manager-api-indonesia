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

// Mendapatkan data POST (email dan password)
$data = json_decode(file_get_contents("php://input"));

// Memastikan data email dan password tidak kosong
if(
    !empty($data->email) &&
    !empty($data->password)
){
    // Menetapkan email untuk pencarian
    $user->email = $data->email;
    $password_input = $data->password; // Simpan password input untuk verifikasi

    // 1. Mencari pengguna berdasarkan email
    if($user->findByEmail()){

        // 2. Verifikasi Password
        // Kita bandingkan password input dengan hash password yang tersimpan di $user->password
        if(password_verify($password_input, $user->password)){
            
            // Login Berhasil!
            http_response_code(200); // 200 OK
            
            // Kembalikan data pengguna (tanpa hash password!)
            echo json_encode(array(
                "message" => "Login Berhasil.",
                "data" => array(
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email
                    // Di sini seharusnya Anda membuat dan mengembalikan Token JWT
                )
            ));

        } else {
            // Verifikasi Password Gagal
            http_response_code(401); // 401 Unauthorized
            echo json_encode(array("message" => "Login Gagal. Email atau kata sandi salah."));
        }
    } else {
        // Pengguna tidak ditemukan (Email tidak ada)
        http_response_code(401); // 401 Unauthorized
        echo json_encode(array("message" => "Login Gagal. Email atau kata sandi salah."));
    }
} else {
    // Data input hilang
    http_response_code(400); // 400 Bad Request
    echo json_encode(array("message" => "Tidak dapat login. Email atau password kosong."));
}

?>