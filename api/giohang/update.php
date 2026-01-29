<?php
/**
 * API Cập nhật số lượng SP trong giỏ
 * POST /api/giohang/update.php
 * Body: { "giohang_id": X, "soluong": Y }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/GiohangDAO.php';
require_once __DIR__ . '/../../dao/BientheDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['giohang_id', 'soluong']);

try {
    $giohangDAO = new GiohangDAO();

    // Lấy thông tin item
    $item = $giohangDAO->getById($data['giohang_id']);
    if (!$item) {
        errorResponse("Không tìm thấy item trong giỏ", 404);
    }

    // Kiểm tra tồn kho nếu tăng số lượng
    if ($data['soluong'] > $item['soluong']) {
        $bientheDAO = new BientheDAO();
        if (!$bientheDAO->checkStock($item['bienthe_id'], $data['soluong'])) {
            errorResponse("Số lượng yêu cầu vượt quá tồn kho", 400);
        }
    }

    // Cập nhật
    if ($giohangDAO->updateQuantity($data['giohang_id'], $data['soluong'])) {
        $cart = $giohangDAO->getCartWithTotal($item['user_id']);
        jsonResponse($cart, "Cập nhật thành công");
    } else {
        errorResponse("Không thể cập nhật", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
