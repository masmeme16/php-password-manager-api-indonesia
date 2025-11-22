<?php

class VaultItem {
    // Properti koneksi database
    private $conn;
    private $table_name = "vault_items";

    // Properti Objek (sesuai kolom tabel vault_items)
    public $id;
    public $user_id;
    public $title;
    public $username;
    public $password_encrypted; // Menyimpan data terenkripsi
    public $url;
    public $notes;
    public $created_at;

    /**
     * Konstruktor dengan koneksi database
     * @param PDO $db Objek koneksi PDO
     */
    public function __construct($db){
        $this->conn = $db;
    }

    // --- METODE CRUD ---

    /**
     * Membuat item brankas baru
     * @return bool True jika berhasil
     */
    public function create(){
        $query = "INSERT INTO " . $this->table_name . "
                  SET 
                      user_id = :user_id,
                      title = :title,
                      username = :username,
                      password_encrypted = :password_encrypted,
                      url = :url,
                      notes = :notes";

        $stmt = $this->conn->prepare($query);

        // Sanitasi input
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password_encrypted = htmlspecialchars(strip_tags($this->password_encrypted));
        $this->url = htmlspecialchars(strip_tags($this->url));
        $this->notes = htmlspecialchars(strip_tags($this->notes));

        // Bind nilai ke parameter
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password_encrypted', $this->password_encrypted);
        $stmt->bindParam(':url', $this->url);
        $stmt->bindParam(':notes', $this->notes);

        if($stmt->execute()){
            return true;
        }

        return false;
    }

    /**
     * Membaca semua item brankas untuk user tertentu
     * @return PDOStatement Statement PDO
     */
    public function readAllByUser(){
        // Hanya ambil item yang dimiliki oleh user_id ini
        $query = "SELECT 
                      id, title, username, password_encrypted, url, notes, created_at
                  FROM 
                      " . $this->table_name . "
                  WHERE 
                      user_id = :user_id
                  ORDER BY
                      created_at DESC";

        $stmt = $this->conn->prepare($query);
        
        // Bind user_id
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $stmt->bindParam(':user_id', $this->user_id);

        $stmt->execute();

        return $stmt;
    }
    
    /**
     * Memperbarui item brankas tertentu
     * @return bool True jika berhasil
     */
    public function update(){
        $query = "UPDATE " . $this->table_name . "
                  SET
                      title = :title,
                      username = :username,
                      password_encrypted = :password_encrypted,
                      url = :url,
                      notes = :notes
                  WHERE
                      id = :id AND user_id = :user_id"; // Penting: Hanya user_id pemilik yang bisa update

        $stmt = $this->conn->prepare($query);

        // Sanitasi input
        $this->id = htmlspecialchars(strip_tags($this->id));
        // ... (Sanitasi properti lain seperti di metode create())

        // Bind nilai ke parameter
        $stmt->bindParam(':id', $this->id);
        // Bind properti lainnya...
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password_encrypted', $this->password_encrypted);
        $stmt->bindParam(':url', $this->url);
        $stmt->bindParam(':notes', $this->notes);

        if($stmt->execute()){
            // Cek apakah ada baris yang terpengaruh (artinya item ditemukan dan diupdate)
            return $stmt->rowCount() > 0;
        }

        return false;
    }

    /**
     * Menghapus item brankas tertentu
     * @return bool True jika berhasil
     */
    public function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        // Sanitasi dan Bind ID & User ID
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id); // Penting: Hanya user_id pemilik yang bisa delete

        if($stmt->execute()){
            // Cek apakah ada baris yang terpengaruh
            return $stmt->rowCount() > 0;
        }

        return false;
    }
}
?>