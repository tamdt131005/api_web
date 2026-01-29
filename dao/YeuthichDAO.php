<?php
/**
 * YeuthichDAO - Quản lý bảng yeuthich (Sản phẩm yêu thích)
 */

require_once __DIR__ . '/../config/database.php';

class YeuthichDAO
{
    private $conn;
    private $table = "yeuthich";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy danh sách SP yêu thích của user
     */
    public function findByUser($user_id)
    {
        $sql = "SELECT yt.*, sp.tensanpham, sp.giaban, sp.giakhuyenmai, sp.hinhanh, sp.slug
                FROM {$this->table} yt
                LEFT JOIN sanpham sp ON yt.sanpham_id = sp.sanpham_id
                WHERE yt.user_id = :user_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Kiểm tra SP đã yêu thích chưa
     */
    public function checkExists($user_id, $sanpham_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND sanpham_id = :sanpham_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':sanpham_id', $sanpham_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() ? true : false;
    }

    /**
     * Thêm vào yêu thích
     */
    public function add($user_id, $sanpham_id)
    {
        // Kiểm tra đã tồn tại chưa
        if ($this->checkExists($user_id, $sanpham_id)) {
            return true; // Đã có rồi
        }

        $sql = "INSERT INTO {$this->table} (user_id, sanpham_id) 
                VALUES (:user_id, :sanpham_id)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':sanpham_id', $sanpham_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Xóa khỏi yêu thích
     */
    public function remove($user_id, $sanpham_id)
    {
        $sql = "DELETE FROM {$this->table} 
                WHERE user_id = :user_id AND sanpham_id = :sanpham_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':sanpham_id', $sanpham_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Toggle yêu thích (thêm nếu chưa có, xóa nếu đã có)
     */
    public function toggle($user_id, $sanpham_id)
    {
        if ($this->checkExists($user_id, $sanpham_id)) {
            $this->remove($user_id, $sanpham_id);
            return ['action' => 'removed', 'liked' => false];
        } else {
            $this->add($user_id, $sanpham_id);
            return ['action' => 'added', 'liked' => true];
        }
    }

    /**
     * Đếm số SP yêu thích của user
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

    /**
     * Đếm số lượt yêu thích của SP
     */
    public function countBySanpham($sanpham_id)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE sanpham_id = :sanpham_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':sanpham_id', $sanpham_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}
