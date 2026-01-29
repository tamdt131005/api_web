<?php
/**
 * Admin API: Xóa danh mục
 * DELETE /api/admin/categories/delete.php?id=...
 */

require_once __DIR__ . '/../../../config/response.php';
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$categoryId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($categoryId <= 0) {
    $data = getJsonInput();
    $categoryId = isset($data['danhmuc_id']) ? (int) $data['danhmuc_id'] : 0;
}

if ($categoryId <= 0) {
    errorResponse("Thiếu ID danh mục", 400);
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        errorResponse("Lỗi kết nối database", 500);
    }

    // Kiểm tra có sản phẩm trong danh mục không
    $checkSql = "SELECT COUNT(*) as count FROM sanpham WHERE danhmuc_id = :id";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([':id' => $categoryId]);
    $check = $checkStmt->fetch();

    if ($check['count'] > 0) {
        errorResponse("Không thể xóa danh mục có sản phẩm", 400);
    }

    $sql = "DELETE FROM danhmuc WHERE danhmuc_id = :id";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([':id' => $categoryId]);

    if ($result) {
        jsonResponse(['danhmuc_id' => $categoryId], "Xóa danh mục thành công");
    } else {
        errorResponse("Không thể xóa", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi: " . $e->getMessage(), 500);
}
