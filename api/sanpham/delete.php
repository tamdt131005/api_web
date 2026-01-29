<?php
/**
 * API Xóa sản phẩm
 * POST /api/sanpham/delete.php
 * Body: { "sanpham_id": X }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/SanphamDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['sanpham_id']);

try {
    $sanphamDAO = new SanphamDAO();

    // Kiểm tra tồn tại
    $product = $sanphamDAO->getById($data['sanpham_id']);
    if (!$product) {
        errorResponse("Không tìm thấy sản phẩm", 404);
    }

    if ($sanphamDAO->delete($data['sanpham_id'])) {
        jsonResponse(null, "Xóa sản phẩm thành công");
    } else {
        errorResponse("Không thể xóa sản phẩm", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
