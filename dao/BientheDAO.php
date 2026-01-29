<?php
/**
 * BientheDAO - Quản lý bảng bienthesp (Biến thể sản phẩm: size, màu)
 */

require_once __DIR__ . '/../config/database.php';

class BientheDAO
{
    private $conn;
    private $table = "bienthesp";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy tất cả biến thể
     */
    public function getAll()
    {
        $sql = "SELECT bt.*, sp.tensanpham 
                FROM {$this->table} bt
                LEFT JOIN sanpham sp ON bt.sanpham_id = sp.sanpham_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy biến thể theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT bt.*, sp.tensanpham, sp.giaban, sp.giakhuyenmai 
                FROM {$this->table} bt
                LEFT JOIN sanpham sp ON bt.sanpham_id = sp.sanpham_id
                WHERE bt.bienthe_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lấy tất cả biến thể của sản phẩm
     */
    public function findBySanpham($sanpham_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE sanpham_id = :sanpham_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':sanpham_id', $sanpham_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách size của sản phẩm
     */
    public function getSizes($sanpham_id)
    {
        $sql = "SELECT DISTINCT kichthuoc FROM {$this->table} 
                WHERE sanpham_id = :sanpham_id AND soluong > 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':sanpham_id', $sanpham_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Lấy danh sách màu của sản phẩm
     */
    public function getColors($sanpham_id)
    {
        $sql = "SELECT DISTINCT mausac FROM {$this->table} 
                WHERE sanpham_id = :sanpham_id AND soluong > 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':sanpham_id', $sanpham_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Tìm biến thể theo SP, size và màu
     */
    public function findByAttributes($sanpham_id, $size, $color)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE sanpham_id = :sanpham_id 
                AND kichthuoc = :size 
                AND mausac = :color";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':sanpham_id', $sanpham_id, PDO::PARAM_INT);
        $stmt->bindParam(':size', $size);
        $stmt->bindParam(':color', $color);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Kiểm tra tồn kho
     */
    public function checkStock($bienthe_id, $quantity = 1)
    {
        $sql = "SELECT soluong FROM {$this->table} WHERE bienthe_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $bienthe_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result && $result['soluong'] >= $quantity) {
            return true;
        }
        return false;
    }

    /**
     * Cập nhật số lượng tồn kho
     */
    public function updateStock($id, $quantity)
    {
        $sql = "UPDATE {$this->table} SET soluong = :soluong WHERE bienthe_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':soluong', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Giảm số lượng tồn kho (khi đặt hàng)
     */
    public function decreaseStock($id, $quantity)
    {
        $sql = "UPDATE {$this->table} SET soluong = soluong - :quantity 
                WHERE bienthe_id = :id AND soluong >= :quantity2";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':quantity2', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Tăng số lượng tồn kho (khi hủy đơn)
     */
    public function increaseStock($id, $quantity)
    {
        $sql = "UPDATE {$this->table} SET soluong = soluong + :quantity WHERE bienthe_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Tạo biến thể mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (sanpham_id, kichthuoc, mausac, soluong, hinhanh) 
                VALUES (:sanpham_id, :kichthuoc, :mausac, :soluong, :hinhanh)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':sanpham_id', $data['sanpham_id'], PDO::PARAM_INT);
        $stmt->bindParam(':kichthuoc', $data['kichthuoc']);
        $stmt->bindParam(':mausac', $data['mausac']);
        $stmt->bindParam(':soluong', $data['soluong'], PDO::PARAM_INT);
        $stmt->bindParam(':hinhanh', $data['hinhanh']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật biến thể
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                kichthuoc = :kichthuoc,
                mausac = :mausac,
                soluong = :soluong,
                hinhanh = :hinhanh
                WHERE bienthe_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':kichthuoc', $data['kichthuoc']);
        $stmt->bindParam(':mausac', $data['mausac']);
        $stmt->bindParam(':soluong', $data['soluong'], PDO::PARAM_INT);
        $stmt->bindParam(':hinhanh', $data['hinhanh']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Xóa biến thể
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE bienthe_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
