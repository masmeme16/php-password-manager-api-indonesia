<?php

class Database {
    // Properti koneksi
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    public $conn;

    /**
     * Metode untuk mendapatkan koneksi database
     * @return PDO|null Objek koneksi PDO atau null jika gagal
     */
    public function getConnection(){
        $this->conn = null;

        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Mengaktifkan pengecualian (exceptions)
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Mengambil hasil sebagai array asosiatif
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try{
            // Mencoba membuat koneksi baru
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            // echo "Koneksi Berhasil!"; // Hanya untuk pengujian
        }catch(PDOException $exception){
            // Tangani error koneksi
            error_log("Connection error: " . $exception->getMessage());
            die("Connection error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>