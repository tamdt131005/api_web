<?php
/**
 * PhieunhapDAO - Quản lý bảng phieunhap và chitietphieunhap (Nhập kho)
 */

require_once __DIR__ . '/../config/database.php';

class PhieunhapDAO
{
    private $conn;
    private $table = "phieunhap";
    private $tableChitiet = "chitietphieunhap";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy tất cả phiếu nhập
     */
    public function getAll($page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM {$this->table} 
                ORDER BY ngaynhap DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy phiếu nhập theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE phieunhap_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lấy chi tiết phiếu nhập
     */
    public function getChitiet($phieunhap_id)
    {
        $sql = "SELECT ct.*, bt.kichthuoc, bt.mausac, sp.tensanpham 
                FROM {$this->tableChitiet} ct
                LEFT JOIN bienthesp bt ON ct.bienthe_id = bt.bienthe_id
                LEFT JOIN sanpham sp ON bt.sanpham_id = sp.sanpham_id
                WHERE ct.phieunhap_id = :phieunhap_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':phieunhap_id', $phieunhap_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tạo phiếu nhập mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (tongtien, ghichu) 
                VALUES (:tongtien, :ghichu)";

        $stmt = $this->conn->prepare($sql);
        $tongtien = $data['tongtien'] ?? 0;
        $ghichu = $data['ghichu'] ?? '';

        $stmt->bindParam(':tongtien', $tongtien);
        $stmt->bindParam(':ghichu', $ghichu);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Thêm chi tiết phiếu nhập
     */
    public function addChitiet($data)
    {
        $sql = "INSERT INTO {$this->tableChitiet} 
                (phieunhap_id, bienthe_id, soluong, dongia, ghichu) 
                VALUES 
                (:phieunhap_id, :bienthe_id, :soluong, :dongia, :ghichu)";

        $stmt = $this->conn->prepare($sql);
        $ghichu = $data['ghichu'] ?? '';

        $stmt->bindParam(':phieunhap_id', $data['phieunhap_id'], PDO::PARAM_INT);
        $stmt->bindParam(':bienthe_id', $data['bienthe_id'], PDO::PARAM_INT);
        $stmt->bindParam(':soluong', $data['soluong'], PDO::PARAM_INT);
        $stmt->bindParam(':dongia', $data['dongia']);
        $stmt->bindParam(':ghichu', $ghichu);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Tạo phiếu nhập hoàn chỉnh (header + details)
     */
    public function createWithDetails($headerData, $details)
    {
        try {
            $this->conn->beginTransaction();

            // Tính tổng tiền
            $tongtien = 0;
            foreach ($details as $item) {
                $tongtien += $item['soluong'] * $item['dongia'];
            }
            $headerData['tongtien'] = $tongtien;

            // Tạo phiếu nhập
            $phieunhap_id = $this->create($headerData);
            if (!$phieunhap_id) {
                throw new Exception("Không thể tạo phiếu nhập");
            }

            // Thêm chi tiết và cập nhật tồn kho
            require_once __DIR__ . '/BientheDAO.php';
            $bientheDAO = new BientheDAO();

            foreach ($details as $item) {
                $item['phieunhap_id'] = $phieunhap_id;
                $this->addChitiet($item);

                // Cập nhật tồn kho biến thể
                $bientheDAO->increaseStock($item['bienthe_id'], $item['soluong']);
            }

            $this->conn->commit();
            return $phieunhap_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Cập nhật tổng tiền phiếu nhập
     */
    public function updateTongtien($id)
    {
        $sql = "UPDATE {$this->table} SET 
                tongtien = (SELECT SUM(thanhtien) FROM {$this->tableChitiet} WHERE phieunhap_id = :id)
                WHERE phieunhap_id = :id2";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':id2', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Xóa phiếu nhập
     */
    public function delete($id)
    {
        // Chi tiết sẽ tự động xóa do CASCADE
        $sql = "DELETE FROM {$this->table} WHERE phieunhap_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
