<?php
/**
 * DanhgiaDAO - Quản lý bảng danhgia (Đánh giá sản phẩm)
 */

require_once __DIR__ . '/../config/database.php';

class DanhgiaDAO
{
    private $conn;
    private $table = "danhgia";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy tất cả đánh giá của sản phẩm
     */
    public function findBySanpham($sanpham_id)
    {
        $sql = "SELECT dg.*, u.fullname, u.avatar 
                FROM {$this->table} dg
                LEFT JOIN users u ON dg.user_id = u.user_id
                WHERE dg.sanpham_id = :sanpham_id
                ORDER BY dg.danhgia_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':sanpham_id', $sanpham_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy đánh giá của user
     */
    public function findByUser($user_id)
    {
        $sql = "SELECT dg.*, sp.tensanpham, sp.hinhanh 
                FROM {$this->table} dg
                LEFT JOIN sanpham sp ON dg.sanpham_id = sp.sanpham_id
                WHERE dg.user_id = :user_id
                ORDER BY dg.danhgia_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy đánh giá theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT dg.*, u.fullname, sp.tensanpham 
                FROM {$this->table} dg
                LEFT JOIN users u ON dg.user_id = u.user_id
                LEFT JOIN sanpham sp ON dg.sanpham_id = sp.sanpham_id
                WHERE dg.danhgia_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Kiểm tra user đã đánh giá SP chưa
     */
    public function checkExists($user_id, $sanpham_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND sanpham_id = :sanpham_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':sanpham_id', $sanpham_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Tính điểm đánh giá trung bình
     */
    public function getAverageRating($sanpham_id)
    {
        $sql = "SELECT AVG(danhgia) as avg_rating, COUNT(*) as total_reviews 
                FROM {$this->table} WHERE sanpham_id = :sanpham_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':sanpham_id', $sanpham_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();

        return [
            'avg_rating' => round($result['avg_rating'], 1) ?? 0,
            'total_reviews' => $result['total_reviews'] ?? 0
        ];
    }

    /**
     * Tạo đánh giá mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (sanpham_id, user_id, danhgia, tieude, mota) 
                VALUES (:sanpham_id, :user_id, :danhgia, :tieude, :mota)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':sanpham_id', $data['sanpham_id'], PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':danhgia', $data['danhgia'], PDO::PARAM_INT);
        $stmt->bindParam(':tieude', $data['tieude']);
        $stmt->bindParam(':mota', $data['mota']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật đánh giá
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                danhgia = :danhgia,
                tieude = :tieude,
                mota = :mota
                WHERE danhgia_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':danhgia', $data['danhgia'], PDO::PARAM_INT);
        $stmt->bindParam(':tieude', $data['tieude']);
        $stmt->bindParam(':mota', $data['mota']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Xóa đánh giá
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE danhgia_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
