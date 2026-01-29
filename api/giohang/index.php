<?php
/**
 * API Giỏ hàng - Danh sách & Thêm mới
 * GET /api/giohang/?user_id=X - Lấy giỏ hàng
 * POST /api/giohang/ - Thêm SP vào giỏ
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/GiohangDAO.php';
require_once __DIR__ . '/../../dao/BientheDAO.php';

try {
    $giohangDAO = new GiohangDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $userId = $_GET['user_id'] ?? null;

        if (!$userId) {
            errorResponse("Thiếu user_id", 400);
        }

        $cart = $giohangDAO->getCartWithTotal($userId);
        jsonResponse($cart);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = getJsonInput();
        validateRequired($data, ['user_id', 'sanpham_id', 'bienthe_id', 'soluong']);

        // Kiểm tra tồn kho
        $bientheDAO = new BientheDAO();
        if (!$bientheDAO->checkStock($data['bienthe_id'], $data['soluong'])) {
            errorResponse("Số lượng sản phẩm không đủ trong kho", 400);
        }

        $result = $giohangDAO->add($data);

        if ($result) {
            $cart = $giohangDAO->getCartWithTotal($data['user_id']);
            jsonResponse($cart, "Thêm vào giỏ hàng thành công");
        } else {
            errorResponse("Không thể thêm vào giỏ hàng", 500);
        }

    } else {
        errorResponse("Method không được hỗ trợ", 405);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
