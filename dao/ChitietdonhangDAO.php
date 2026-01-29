<?php
/**
 * ChitietdonhangDAO - Quản lý bảng chitietdonhang
 */

require_once __DIR__ . '/../config/database.php';

class ChitietdonhangDAO
{
    private $conn;
    private $table = "chitietdonhang";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy chi tiết của đơn hàng
     */
    public function findByDonhang($donhang_id)
    {
        $sql = "SELECT ct.*, 
                sp.tensanpham, sp.giaban, sp.giakhuyenmai, sp.hinhanh as sp_hinhanh,
                bt.kichthuoc, bt.mausac, bt.hinhanh as bt_hinhanh
                FROM {$this->table} ct
                LEFT JOIN sanpham sp ON ct.sanpham_id = sp.sanpham_id
                LEFT JOIN bienthesp bt ON ct.bienthe_id = bt.bienthe_id
                WHERE ct.donhang_id = :donhang_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':donhang_id', $donhang_id, PDO::PARAM_INT);
        $stmt->execute();

        $items = $stmt->fetchAll();

        // Tính thành tiền cho từng item
        foreach ($items as &$item) {
            $price = ($item['giakhuyenmai'] && $item['giakhuyenmai'] > 0)
                ? $item['giakhuyenmai']
                : $item['giaban'];
            $item['gia'] = $price;
            $item['thanhtien'] = $price * $item['soluong'];
        }

        return $items;
    }

    /**
     * Thêm chi tiết đơn hàng
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (donhang_id, sanpham_id, bienthe_id, soluong) 
                VALUES (:donhang_id, :sanpham_id, :bienthe_id, :soluong)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':donhang_id', $data['donhang_id'], PDO::PARAM_INT);
        $stmt->bindParam(':sanpham_id', $data['sanpham_id'], PDO::PARAM_INT);
        $stmt->bindParam(':bienthe_id', $data['bienthe_id'], PDO::PARAM_INT);
        $stmt->bindParam(':soluong', $data['soluong'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Thêm nhiều chi tiết từ giỏ hàng
     */
    public function createFromCart($donhang_id, $cartItems)
    {
        $success = true;
        foreach ($cartItems as $item) {
            $data = [
                'donhang_id' => $donhang_id,
                'sanpham_id' => $item['sanpham_id'],
                'bienthe_id' => $item['bienthe_id'],
                'soluong' => $item['soluong']
            ];
            if (!$this->create($data)) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Xóa tất cả chi tiết của đơn hàng
     */
    public function deleteByDonhang($donhang_id)
    {
        $sql = "DELETE FROM {$this->table} WHERE donhang_id = :donhang_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':donhang_id', $donhang_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Lấy chi tiết theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT ct.*, 
                sp.tensanpham, sp.giaban, sp.giakhuyenmai,
                bt.kichthuoc, bt.mausac
                FROM {$this->table} ct
                LEFT JOIN sanpham sp ON ct.sanpham_id = sp.sanpham_id
                LEFT JOIN bienthesp bt ON ct.bienthe_id = bt.bienthe_id
                WHERE ct.chitiet_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}
