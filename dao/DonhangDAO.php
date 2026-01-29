<?php
/**
 * DonhangDAO - Quản lý bảng donhang (Đơn hàng)
 */

require_once __DIR__ . '/../config/database.php';

class DonhangDAO
{
    private $conn;
    private $table = "donhang";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy tất cả đơn hàng (admin)
     */
    public function getAll($page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT dh.*, u.fullname, u.phone as user_phone,
                dc.tennguoinhan, dc.sodienthoai, dc.diachichitiet, dc.phuong, dc.tinh
                FROM {$this->table} dh
                LEFT JOIN users u ON dh.user_id = u.user_id
                LEFT JOIN diachigiaohang dc ON dh.diachi_id = dc.diachi_id
                ORDER BY dh.ngaytao DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy đơn hàng của user
     */
    public function findByUser($user_id, $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT dh.*, 
                dc.tennguoinhan, dc.sodienthoai, dc.diachichitiet, dc.phuong, dc.tinh
                FROM {$this->table} dh
                LEFT JOIN diachigiaohang dc ON dh.diachi_id = dc.diachi_id
                WHERE dh.user_id = :user_id
                ORDER BY dh.ngaytao DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy đơn hàng theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT dh.*, u.fullname, u.email, u.phone as user_phone,
                dc.tennguoinhan, dc.sodienthoai, dc.diachichitiet, dc.phuong, dc.tinh
                FROM {$this->table} dh
                LEFT JOIN users u ON dh.user_id = u.user_id
                LEFT JOIN diachigiaohang dc ON dh.diachi_id = dc.diachi_id
                WHERE dh.donhang_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lấy đơn hàng theo trạng thái
     */
    public function findByStatus($status, $page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT dh.*, u.fullname,
                dc.tennguoinhan, dc.sodienthoai, dc.diachichitiet
                FROM {$this->table} dh
                LEFT JOIN users u ON dh.user_id = u.user_id
                LEFT JOIN diachigiaohang dc ON dh.diachi_id = dc.diachi_id
                WHERE dh.trangthai = :status
                ORDER BY dh.ngaytao DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tạo đơn hàng mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (user_id, diachi_id, ghichu, trangthai, phuongthucthanhtoan, 
                trangthaithanhtoan, tongtienhang, phivanchuyen, tongthanhtoan) 
                VALUES 
                (:user_id, :diachi_id, :ghichu, :trangthai, :phuongthucthanhtoan,
                :trangthaithanhtoan, :tongtienhang, :phivanchuyen, :tongthanhtoan)";

        $stmt = $this->conn->prepare($sql);

        $trangthai = $data['trangthai'] ?? 'choxacnhan';
        $trangthaithanhtoan = $data['trangthaithanhtoan'] ?? 'chuathanhtoan';

        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':diachi_id', $data['diachi_id'], PDO::PARAM_INT);
        $stmt->bindParam(':ghichu', $data['ghichu']);
        $stmt->bindParam(':trangthai', $trangthai);
        $stmt->bindParam(':phuongthucthanhtoan', $data['phuongthucthanhtoan']);
        $stmt->bindParam(':trangthaithanhtoan', $trangthaithanhtoan);
        $stmt->bindParam(':tongtienhang', $data['tongtienhang']);
        $stmt->bindParam(':phivanchuyen', $data['phivanchuyen']);
        $stmt->bindParam(':tongthanhtoan', $data['tongthanhtoan']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus($id, $status, $lydo = null)
    {
        $sql = "UPDATE {$this->table} SET trangthai = :status";
        if ($lydo !== null) {
            $sql .= ", lydo = :lydo";
        }
        $sql .= " WHERE donhang_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($lydo !== null) {
            $stmt->bindParam(':lydo', $lydo);
        }

        return $stmt->execute();
    }

    /**
     * Cập nhật trạng thái thanh toán
     */
    public function updatePaymentStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET trangthaithanhtoan = :status WHERE donhang_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel($id, $lydo)
    {
        return $this->updateStatus($id, 'dahuy', $lydo);
    }

    /**
     * Đếm đơn hàng theo trạng thái
     */
    public function countByStatus($status)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE trangthai = :status";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Thống kê doanh thu theo ngày
     */
    public function revenueByDate($startDate, $endDate)
    {
        $sql = "SELECT DATE(ngaytao) as ngay, 
                SUM(tongthanhtoan) as doanhthu, 
                COUNT(*) as sodonhang
                FROM {$this->table}
                WHERE trangthai = 'dagiao' 
                AND DATE(ngaytao) BETWEEN :start AND :end
                GROUP BY DATE(ngaytao)
                ORDER BY ngay";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':start', $startDate);
        $stmt->bindParam(':end', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Xóa đơn hàng
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE donhang_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
