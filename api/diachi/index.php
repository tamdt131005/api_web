<?php
/**
 * API Địa chỉ giao hàng - Danh sách & Tạo mới
 * GET /api/diachi/?user_id=X - Danh sách địa chỉ
 * POST /api/diachi/ - Tạo địa chỉ mới
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DiachigiaohangDAO.php';

try {
    $diachiDAO = new DiachigiaohangDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $userId = $_GET['user_id'] ?? null;

        if (!$userId) {
            errorResponse("Thiếu user_id", 400);
        }

        $addresses = $diachiDAO->findByUser($userId);
        jsonResponse($addresses);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = getJsonInput();
        validateRequired($data, ['user_id', 'tennguoinhan', 'sodienthoai', 'diachichitiet', 'tinh']);

        $addressId = $diachiDAO->create($data);

        if ($addressId) {
            $newAddress = $diachiDAO->getById($addressId);
            jsonResponse($newAddress, "Thêm địa chỉ thành công", 201);
        } else {
            errorResponse("Không thể thêm địa chỉ", 500);
        }

    } else {
        errorResponse("Method không được hỗ trợ", 405);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
