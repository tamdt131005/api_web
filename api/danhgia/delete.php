<?php
/**
 * API Xóa đánh giá
 * POST /api/danhgia/delete.php
 * Body: { "danhgia_id": X }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DanhgiaDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['danhgia_id']);

try {
    $danhgiaDAO = new DanhgiaDAO();

    $review = $danhgiaDAO->getById($data['danhgia_id']);
    if (!$review) {
        errorResponse("Không tìm thấy đánh giá", 404);
    }

    if ($danhgiaDAO->delete($data['danhgia_id'])) {
        jsonResponse(null, "Xóa đánh giá thành công");
    } else {
        errorResponse("Không thể xóa", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
