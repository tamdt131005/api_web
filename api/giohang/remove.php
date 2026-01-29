<?php
/**
 * API Xóa SP khỏi giỏ hàng
 * POST /api/giohang/remove.php
 * Body: { "giohang_id": X }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/GiohangDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['giohang_id']);

try {
    $giohangDAO = new GiohangDAO();

    // Lấy thông tin item để lấy user_id
    $item = $giohangDAO->getById($data['giohang_id']);
    if (!$item) {
        errorResponse("Không tìm thấy item trong giỏ", 404);
    }

    $userId = $item['user_id'];

    if ($giohangDAO->remove($data['giohang_id'])) {
        $cart = $giohangDAO->getCartWithTotal($userId);
        jsonResponse($cart, "Xóa thành công");
    } else {
        errorResponse("Không thể xóa", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
