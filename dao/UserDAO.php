<?php
/**
 * UserDAO - Quản lý bảng users
 */

require_once __DIR__ . '/../config/database.php';

class UserDAO
{
    private $conn;
    private $table = "users";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy tất cả users
     */
    public function getAll()
    {
        $sql = "SELECT user_id, fullname, sex, ngaysinh, email, phone, avatar, 
                create_at, username, role, status 
                FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy user theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT user_id, fullname, sex, ngaysinh, email, phone, avatar, 
                create_at, username, role, status 
                FROM {$this->table} WHERE user_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Tìm user theo username
     */
    public function findByUsername($username)
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Tìm user theo email
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Tạo user mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (fullname, sex, ngaysinh, email, phone, avatar, username, password, role, status) 
                VALUES 
                (:fullname, :sex, :ngaysinh, :email, :phone, :avatar, :username, :password, :role, :status)";

        $stmt = $this->conn->prepare($sql);

        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bindParam(':fullname', $data['fullname']);
        $stmt->bindParam(':sex', $data['sex']);
        $stmt->bindParam(':ngaysinh', $data['ngaysinh']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':avatar', $data['avatar']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':status', $data['status']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật thông tin user
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                fullname = :fullname,
                sex = :sex,
                ngaysinh = :ngaysinh,
                email = :email,
                phone = :phone,
                avatar = :avatar
                WHERE user_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fullname', $data['fullname']);
        $stmt->bindParam(':sex', $data['sex']);
        $stmt->bindParam(':ngaysinh', $data['ngaysinh']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':avatar', $data['avatar']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword($id, $newPassword)
    {
        $sql = "UPDATE {$this->table} SET password = :password WHERE user_id = :id";
        $stmt = $this->conn->prepare($sql);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Xác thực mật khẩu
     */
    public function verifyPassword($username, $password)
    {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // Không trả về password
            return $user;
        }
        return false;
    }

    /**
     * Xóa user
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
