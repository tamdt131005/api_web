<?php
/**
 * DiachigiaohangDAO - Quản lý bảng diachigiaohang
 */

require_once __DIR__ . '/../config/database.php';

class DiachigiaohangDAO
{
    private $conn;
    private $table = "diachigiaohang";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy tất cả địa chỉ của user
     */
    public function findByUser($user_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id 
                ORDER BY macdinh DESC, diachi_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy địa chỉ theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE diachi_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lấy địa chỉ mặc định của user
     */
    public function getDefault($user_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND macdinh = '1'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Tạo địa chỉ mới
     */
    public function create($data)
    {
        // Nếu là địa chỉ mặc định, bỏ mặc định các địa chỉ khác
        if (isset($data['macdinh']) && $data['macdinh'] == '1') {
            $this->clearDefault($data['user_id']);
        }

        $sql = "INSERT INTO {$this->table} 
                (user_id, tennguoinhan, sodienthoai, diachichitiet, phuong, tinh, macdinh) 
                VALUES 
                (:user_id, :tennguoinhan, :sodienthoai, :diachichitiet, :phuong, :tinh, :macdinh)";

        $stmt = $this->conn->prepare($sql);
        $macdinh = $data['macdinh'] ?? '0';

        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':tennguoinhan', $data['tennguoinhan']);
        $stmt->bindParam(':sodienthoai', $data['sodienthoai']);
        $stmt->bindParam(':diachichitiet', $data['diachichitiet']);
        $stmt->bindParam(':phuong', $data['phuong']);
        $stmt->bindParam(':tinh', $data['tinh']);
        $stmt->bindParam(':macdinh', $macdinh);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật địa chỉ
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                tennguoinhan = :tennguoinhan,
                sodienthoai = :sodienthoai,
                diachichitiet = :diachichitiet,
                phuong = :phuong,
                tinh = :tinh
                WHERE diachi_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tennguoinhan', $data['tennguoinhan']);
        $stmt->bindParam(':sodienthoai', $data['sodienthoai']);
        $stmt->bindParam(':diachichitiet', $data['diachichitiet']);
        $stmt->bindParam(':phuong', $data['phuong']);
        $stmt->bindParam(':tinh', $data['tinh']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Đặt làm địa chỉ mặc định
     */
    public function setDefault($id, $user_id)
    {
        // Bỏ mặc định tất cả
        $this->clearDefault($user_id);

        // Đặt mặc định cho địa chỉ được chọn
        $sql = "UPDATE {$this->table} SET macdinh = '1' WHERE diachi_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Bỏ mặc định tất cả địa chỉ của user
     */
    private function clearDefault($user_id)
    {
        $sql = "UPDATE {$this->table} SET macdinh = '0' WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Xóa địa chỉ
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE diachi_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Đếm số địa chỉ của user
     */
    public function countByUser($user_id)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}
