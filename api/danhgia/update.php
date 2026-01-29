<?php
/**
 * API Cập nhật / Xóa đánh giá
 * POST /api/danhgia/update.php - Cập nhật
 * Body: { "danhgia_id": X, "danhgia": 5, "tieude": "...", "mota": "..." }
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

    $updateData = [
        'danhgia' => $data['danhgia'] ?? $review['danhgia'],
        'tieude' => $data['tieude'] ?? $review['tieude'],
        'mota' => $data['mota'] ?? $review['mota']
    ];

    // Validate điểm đánh giá
    if ($updateData['danhgia'] < 1 || $updateData['danhgia'] > 5) {
        errorResponse("Điểm đánh giá phải từ 1 đến 5", 400);
    }

    if ($danhgiaDAO->update($data['danhgia_id'], $updateData)) {
        $updated = $danhgiaDAO->getById($data['danhgia_id']);
        jsonResponse($updated, "Cập nhật thành công");
    } else {
        errorResponse("Không thể cập nhật", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
