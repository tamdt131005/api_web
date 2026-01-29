<?php
/**
 * DanhmucDAO - Quản lý bảng danhmuc (Danh mục sản phẩm)
 */

require_once __DIR__ . '/../config/database.php';

class DanhmucDAO
{
    private $conn;
    private $table = "danhmuc";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy tất cả danh mục
     */
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh mục theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE danhmuc_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Tìm danh mục theo slug
     */
    public function findBySlug($slug)
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Tạo danh mục mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (tendanhmuc, slug, mota) 
                VALUES (:tendanhmuc, :slug, :mota)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tendanhmuc', $data['tendanhmuc']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':mota', $data['mota']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật danh mục
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                tendanhmuc = :tendanhmuc,
                slug = :slug,
                mota = :mota
                WHERE danhmuc_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tendanhmuc', $data['tendanhmuc']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':mota', $data['mota']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Xóa danh mục
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE danhmuc_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Đếm số sản phẩm trong danh mục
     */
    public function countProducts($id)
    {
        $sql = "SELECT COUNT(*) as total FROM sanpham WHERE danhmuc_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}
