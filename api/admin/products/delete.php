<?php
/**
 * Admin API: Xóa sản phẩm
 * DELETE /api/admin/products/delete.php?id=...
 */

require_once __DIR__ . '/../../../config/response.php';
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($productId <= 0) {
    $data = getJsonInput();
    $productId = isset($data['sanpham_id']) ? (int) $data['sanpham_id'] : 0;
}

if ($productId <= 0) {
    errorResponse("Thiếu ID sản phẩm", 400);
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        errorResponse("Lỗi kết nối database", 500);
    }

    // Kiểm tra sản phẩm có trong đơn hàng không
    $checkSql = "SELECT COUNT(*) as count FROM chitietdonhang WHERE sanpham_id = :id";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([':id' => $productId]);
    $check = $checkStmt->fetch();

    if ($check['count'] > 0) {
        errorResponse("Không thể xóa sản phẩm đã có trong đơn hàng", 400);
    }

    // Xóa biến thể trước
    $delVariants = $conn->prepare("DELETE FROM bienthesp WHERE sanpham_id = :id");
    $delVariants->execute([':id' => $productId]);

    // Xóa sản phẩm
    $sql = "DELETE FROM sanpham WHERE sanpham_id = :id";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([':id' => $productId]);

    if ($result) {
        jsonResponse(['sanpham_id' => $productId], "Xóa sản phẩm thành công");
    } else {
        errorResponse("Không thể xóa", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi: " . $e->getMessage(), 500);
}
