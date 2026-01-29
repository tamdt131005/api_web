<?php
/**
 * API Xóa toàn bộ giỏ hàng
 * POST /api/giohang/clear.php
 * Body: { "user_id": X }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/GiohangDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['user_id']);

try {
    $giohangDAO = new GiohangDAO();

    if ($giohangDAO->clearCart($data['user_id'])) {
        jsonResponse(null, "Đã xóa toàn bộ giỏ hàng");
    } else {
        errorResponse("Không thể xóa giỏ hàng", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
