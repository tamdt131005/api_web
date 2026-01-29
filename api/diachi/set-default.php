<?php
/**
 * API Đặt địa chỉ mặc định
 * POST /api/diachi/set-default.php
 * Body: { "diachi_id": X, "user_id": X }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DiachigiaohangDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['diachi_id', 'user_id']);

try {
    $diachiDAO = new DiachigiaohangDAO();

    $address = $diachiDAO->getById($data['diachi_id']);
    if (!$address) {
        errorResponse("Không tìm thấy địa chỉ", 404);
    }

    if ($diachiDAO->setDefault($data['diachi_id'], $data['user_id'])) {
        $addresses = $diachiDAO->findByUser($data['user_id']);
        jsonResponse($addresses, "Đã đặt làm địa chỉ mặc định");
    } else {
        errorResponse("Không thể đặt mặc định", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
