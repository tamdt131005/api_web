<?php
/**
 * Admin API: Danh sách danh mục
 * GET /api/admin/categories/
 */

require_once __DIR__ . '/../../../config/response.php';
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse("Method không được hỗ trợ", 405);
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        errorResponse("Lỗi kết nối database", 500);
    }

    $sql = "SELECT dm.*, 
                   (SELECT COUNT(*) FROM sanpham sp WHERE sp.danhmuc_id = dm.danhmuc_id) as so_sanpham
            FROM danhmuc dm
            ORDER BY dm.danhmuc_id DESC";

    $stmt = $conn->query($sql);
    $categories = $stmt->fetchAll();

    jsonResponse(['categories' => $categories]);

} catch (Exception $e) {
    errorResponse("Lỗi: " . $e->getMessage(), 500);
}
