<?php

// Pastikan kelas Database sudah di-include (diperlukan di endpoint)
// require_once __DIR__ . '/../includes/Database.php'; 

class User {
    // Properti koneksi database
    private $conn;
    private $table_name = "users";

    // Properti Objek (sesuai kolom tabel users)
    public $id;
    public $name;
    public $email;
    public $password; // Akan menyimpan password yang sudah di-hash
    public $created_at;

    /**
     * Konstruktor dengan koneksi database
     * @param PDO $db Objek koneksi PDO
     */
    public function __construct($db){
        $this->conn = $db;
    }

    // --- METODE UNTUK REGISTRASI (CREATE) ---

    /**
     * Membuat pengguna baru
     * @return bool True jika berhasil, False jika gagal
     */
    public function register(){
        // Query untuk memasukkan record
        $query = "INSERT INTO " . $this->table_name . "
                  SET 
                      name = :name,
                      email = :email,
                      password = :password";

        // Siapkan statement query
        $stmt = $this->conn->prepare($query);

        // Sanitasi input (menghapus tag HTML, dll.)
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        // Password TIDAK di-sanitasi karena harus di-hash

        // Bind nilai ke parameter
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);

        // Hashing password sebelum disimpan
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);

        // Eksekusi query
        if($stmt->execute()){
            // Ambil ID yang baru dibuat
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        // Tampilkan error jika ada (untuk debugging)
        // printf("Error: %s.\n", $stmt->error);

        return false;
    }

    // --- METODE UNTUK LOGIN (READ) ---

    /**
     * Mencari pengguna berdasarkan email
     * @return bool True jika pengguna ditemukan, False jika tidak
     */
    public function findByEmail(){
        // Query untuk mencari record berdasarkan email
        $query = "SELECT 
                      id, name, email, password, created_at
                  FROM 
                      " . $this->table_name . "
                  WHERE 
                      email = :email
                  LIMIT 
                      0,1";

        // Siapkan statement query
        $stmt = $this->conn->prepare($query);

        // Sanitasi dan Bind nilai email
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(':email', $this->email);

        // Eksekusi query
        $stmt->execute();

        // Ambil jumlah baris
        $num = $stmt->rowCount();

        // Jika email ditemukan
        if($num > 0){
            // Ambil data baris
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set properti objek
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->password = $row['password']; // Ini adalah hash password
            $this->created_at = $row['created_at'];

            return true;
        }

        return false;
    }
    
    // Metode lain (misal: updatePassword) akan ditambahkan di pengembangan selanjutnya...
}
?>