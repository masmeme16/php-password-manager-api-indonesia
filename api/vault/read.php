<?php
// Wajib: Memuat file konfigurasi database dan kelas-kelas inti
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../includes/Database.php';
include_once __DIR__ . '/../../models/VaultItem.php';

// HANYA terima metode GET (atau POST jika Anda ingin mengirim ID pengguna di body)
// Kita gunakan GET dan kirim user_id melalui query parameter untuk kesederhanaan
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // 405 Method Not Allowed
    echo json_encode(array("message" => "Metode request tidak diizinkan."));
    exit();
}

// Mendapatkan koneksi database
$database = new Database();
$db = $database->getConnection();

// Menginisialisasi objek VaultItem
$item = new VaultItem($db);

// Mendapatkan ID Pengguna dari query parameter (misalnya: ?user_id=1)
// Dalam aplikasi nyata, ID ini akan diekstrak dari Token JWT.
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : die();

// Menetapkan user_id ke properti objek
$item->user_id = $user_id;

// Memanggil metode readAllByUser()
$stmt = $item->readAllByUser();
$num = $stmt->rowCount(); // Jumlah baris yang ditemukan

// Cek apakah ada record yang ditemukan
if($num > 0){
    // Array item brankas
    $items_arr = array();
    $items_arr["records"] = array();

    // Ambil isi tabel
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // Ekstrak baris (membuat $id, $title, $username, dll. dari $row['id'])
        extract($row);

        $item_data = array(
            "id" => $id,
            "title" => $title,
            "username" => $username,
            "password_encrypted" => $password_encrypted, // Ingat: ini terenkripsi!
            "url" => $url,
            "notes" => $notes,
            "created_at" => $created_at
        );

        array_push($items_arr["records"], $item_data);
    }

    // Berhasil - kembalikan status code 200 OK
    http_response_code(200);
    echo json_encode($items_arr);

} else {
    // Tidak ada item ditemukan untuk user ini
    http_response_code(404); // 404 Not Found
    echo json_encode(
        array("message" => "Tidak ada item brankas ditemukan untuk pengguna ini.")
    );
}
?>