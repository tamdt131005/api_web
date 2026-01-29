<?php
/**
 * GiohangDAO - Quản lý bảng giohang (Giỏ hàng)
 */

require_once __DIR__ . '/../config/database.php';

class GiohangDAO
{
    private $conn;
    private $table = "giohang";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy giỏ hàng của user (kèm thông tin SP, biến thể, giá)
     */
    public function findByUser($user_id)
    {
        $sql = "SELECT gh.*, 
                sp.tensanpham, sp.giaban, sp.giakhuyenmai, sp.hinhanh as sp_hinhanh,
                bt.kichthuoc, bt.mausac, bt.hinhanh as bt_hinhanh, bt.soluong as tonkho
                FROM {$this->table} gh
                LEFT JOIN sanpham sp ON gh.sanpham_id = sp.sanpham_id
                LEFT JOIN bienthesp bt ON gh.bienthe_id = bt.bienthe_id
                WHERE gh.user_id = :user_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy giỏ hàng kèm tính tổng tiền
     */
    public function getCartWithTotal($user_id)
    {
        $items = $this->findByUser($user_id);
        $total = 0;

        foreach ($items as &$item) {
            // Sử dụng giá khuyến mãi nếu có
            $price = ($item['giakhuyenmai'] && $item['giakhuyenmai'] > 0)
                ? $item['giakhuyenmai']
                : $item['giaban'];
            $item['gia'] = $price;
            $item['thanhtien'] = $price * $item['soluong'];
            $total += $item['thanhtien'];
        }

        return [
            'items' => $items,
            'tongtienhang' => $total,
            'soluong_items' => count($items)
        ];
    }

    /**
     * Đếm số sản phẩm trong giỏ
     */
    public function countItems($user_id)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Tổng số lượng sản phẩm (sum quantity)
     */
    public function sumQuantity($user_id)
    {
        $sql = "SELECT SUM(soluong) as total FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Kiểm tra SP đã có trong giỏ chưa (cùng biến thể)
     */
    public function findExisting($user_id, $sanpham_id, $bienthe_id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id 
                AND sanpham_id = :sanpham_id 
                AND bienthe_id = :bienthe_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':sanpham_id', $sanpham_id, PDO::PARAM_INT);
        $stmt->bindParam(':bienthe_id', $bienthe_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Thêm SP vào giỏ hàng
     */
    public function add($data)
    {
        // Kiểm tra đã có trong giỏ chưa
        $existing = $this->findExisting($data['user_id'], $data['sanpham_id'], $data['bienthe_id']);

        if ($existing) {
            // Cập nhật số lượng
            $newQuantity = $existing['soluong'] + $data['soluong'];
            return $this->updateQuantity($existing['giohang_id'], $newQuantity);
        }

        // Thêm mới
        $sql = "INSERT INTO {$this->table} (user_id, sanpham_id, bienthe_id, soluong) 
                VALUES (:user_id, :sanpham_id, :bienthe_id, :soluong)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':sanpham_id', $data['sanpham_id'], PDO::PARAM_INT);
        $stmt->bindParam(':bienthe_id', $data['bienthe_id'], PDO::PARAM_INT);
        $stmt->bindParam(':soluong', $data['soluong'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật số lượng
     */
    public function updateQuantity($giohang_id, $quantity)
    {
        if ($quantity <= 0) {
            return $this->remove($giohang_id);
        }

        $sql = "UPDATE {$this->table} SET soluong = :soluong WHERE giohang_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':soluong', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $giohang_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Xóa SP khỏi giỏ
     */
    public function remove($giohang_id)
    {
        $sql = "DELETE FROM {$this->table} WHERE giohang_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $giohang_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Xóa toàn bộ giỏ hàng của user
     */
    public function clearCart($user_id)
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Lấy item theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT gh.*, 
                sp.tensanpham, sp.giaban, sp.giakhuyenmai,
                bt.kichthuoc, bt.mausac, bt.soluong as tonkho
                FROM {$this->table} gh
                LEFT JOIN sanpham sp ON gh.sanpham_id = sp.sanpham_id
                LEFT JOIN bienthesp bt ON gh.bienthe_id = bt.bienthe_id
                WHERE gh.giohang_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}
