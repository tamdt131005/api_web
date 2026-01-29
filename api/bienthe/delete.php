<?php
/**
 * API Xóa biến thể
 * POST /api/bienthe/delete.php
 * Body: { "bienthe_id": X }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/BientheDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['bienthe_id']);

try {
    $bientheDAO = new BientheDAO();

    $variant = $bientheDAO->getById($data['bienthe_id']);
    if (!$variant) {
        errorResponse("Không tìm thấy biến thể", 404);
    }

    if ($bientheDAO->delete($data['bienthe_id'])) {
        jsonResponse(null, "Xóa biến thể thành công");
    } else {
        errorResponse("Không thể xóa", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
