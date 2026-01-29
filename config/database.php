<?php
/**
 * Database Configuration - Kết nối MySQL với PDO
 * Sử dụng Prepared Statements để chống SQL Injection
 */

class Database
{
    // Cấu hình kết nối
    // Cấu hình kết nối
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;
    private $charset = "utf8mb4";

    private $conn = null;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: "localhost";
        $this->port = getenv('DB_PORT') ?: "3310"; // Local Laragon default
        $this->dbname = getenv('DB_NAME') ?: "btapweb";
        $this->username = getenv('DB_USER') ?: "root";
        $this->password = getenv('DB_PASSWORD') ?: "";
    }

    /**
     * Lấy kết nối PDO
     * @return PDO|null
     */
    public function getConnection()
    {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

        } catch (PDOException $e) {
            // Trong production, không nên hiển thị chi tiết lỗi
            error_log("Database Connection Error: " . $e->getMessage());
            return null;
        }

        return $this->conn;
    }
}
