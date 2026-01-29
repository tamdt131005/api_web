<?php
/**
 * SanphamDAO - Quản lý bảng sanpham (Sản phẩm)
 */

require_once __DIR__ . '/../config/database.php';

class SanphamDAO
{
    private $conn;
    private $table = "sanpham";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy tất cả sản phẩm (có phân trang)
     */
    public function getAll($page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT sp.*, dm.tendanhmuc 
                FROM {$this->table} sp
                LEFT JOIN danhmuc dm ON sp.danhmuc_id = dm.danhmuc_id
                ORDER BY sp.ngaytao DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Đếm tổng số sản phẩm
     */
    public function countAll()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Lấy sản phẩm theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT sp.*, dm.tendanhmuc 
                FROM {$this->table} sp
                LEFT JOIN danhmuc dm ON sp.danhmuc_id = dm.danhmuc_id
                WHERE sp.sanpham_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Tìm sản phẩm theo slug
     */
    public function findBySlug($slug)
    {
        $sql = "SELECT sp.*, dm.tendanhmuc 
                FROM {$this->table} sp
                LEFT JOIN danhmuc dm ON sp.danhmuc_id = dm.danhmuc_id
                WHERE sp.slug = :slug";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lấy sản phẩm theo danh mục
     */
    public function findByDanhmuc($danhmuc_id, $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT sp.*, dm.tendanhmuc 
                FROM {$this->table} sp
                LEFT JOIN danhmuc dm ON sp.danhmuc_id = dm.danhmuc_id
                WHERE sp.danhmuc_id = :danhmuc_id
                ORDER BY sp.ngaytao DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':danhmuc_id', $danhmuc_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tìm kiếm sản phẩm
     */
    public function search($keyword, $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $searchTerm = "%{$keyword}%";

        $sql = "SELECT sp.*, dm.tendanhmuc 
                FROM {$this->table} sp
                LEFT JOIN danhmuc dm ON sp.danhmuc_id = dm.danhmuc_id
                WHERE sp.tensanpham LIKE :keyword OR sp.mota LIKE :keyword2
                ORDER BY sp.ngaytao DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->bindParam(':keyword2', $searchTerm);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tạo sản phẩm mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (danhmuc_id, tensanpham, slug, mota, giaban, giakhuyenmai, soluong, hinhanh) 
                VALUES 
                (:danhmuc_id, :tensanpham, :slug, :mota, :giaban, :giakhuyenmai, :soluong, :hinhanh)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':danhmuc_id', $data['danhmuc_id'], PDO::PARAM_INT);
        $stmt->bindParam(':tensanpham', $data['tensanpham']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':mota', $data['mota']);
        $stmt->bindParam(':giaban', $data['giaban']);
        $stmt->bindParam(':giakhuyenmai', $data['giakhuyenmai']);
        $stmt->bindParam(':soluong', $data['soluong'], PDO::PARAM_INT);
        $stmt->bindParam(':hinhanh', $data['hinhanh']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                danhmuc_id = :danhmuc_id,
                tensanpham = :tensanpham,
                slug = :slug,
                mota = :mota,
                giaban = :giaban,
                giakhuyenmai = :giakhuyenmai,
                soluong = :soluong,
                hinhanh = :hinhanh
                WHERE sanpham_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':danhmuc_id', $data['danhmuc_id'], PDO::PARAM_INT);
        $stmt->bindParam(':tensanpham', $data['tensanpham']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':mota', $data['mota']);
        $stmt->bindParam(':giaban', $data['giaban']);
        $stmt->bindParam(':giakhuyenmai', $data['giakhuyenmai']);
        $stmt->bindParam(':soluong', $data['soluong'], PDO::PARAM_INT);
        $stmt->bindParam(':hinhanh', $data['hinhanh']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Cập nhật số lượng tồn kho
     */
    public function updateStock($id, $quantity)
    {
        $sql = "UPDATE {$this->table} SET soluong = :soluong WHERE sanpham_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':soluong', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Xóa sản phẩm
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE sanpham_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Lấy sản phẩm mới nhất
     */
    public function getLatest($limit = 8)
    {
        $sql = "SELECT sp.*, dm.tendanhmuc 
                FROM {$this->table} sp
                LEFT JOIN danhmuc dm ON sp.danhmuc_id = dm.danhmuc_id
                ORDER BY sp.ngaytao DESC
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy sản phẩm khuyến mãi
     */
    public function getSale($limit = 8)
    {
        $sql = "SELECT sp.*, dm.tendanhmuc 
                FROM {$this->table} sp
                LEFT JOIN danhmuc dm ON sp.danhmuc_id = dm.danhmuc_id
                WHERE sp.giakhuyenmai IS NOT NULL AND sp.giakhuyenmai > 0
                ORDER BY sp.ngaytao DESC
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
