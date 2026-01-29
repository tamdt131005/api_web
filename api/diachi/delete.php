<?php
/**
 * API Xóa địa chỉ
 * POST /api/diachi/delete.php
 * Body: { "diachi_id": X }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DiachigiaohangDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['diachi_id']);

try {
    $diachiDAO = new DiachigiaohangDAO();

    $address = $diachiDAO->getById($data['diachi_id']);
    if (!$address) {
        errorResponse("Không tìm thấy địa chỉ", 404);
    }

    if ($diachiDAO->delete($data['diachi_id'])) {
        jsonResponse(null, "Xóa địa chỉ thành công");
    } else {
        errorResponse("Không thể xóa", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
