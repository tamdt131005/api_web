<?php
/**
 * API Cập nhật / Xóa biến thể
 * POST /api/bienthe/update.php - Cập nhật
 * Body: { "bienthe_id": X, ... }
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

    $updateData = [
        'kichthuoc' => $data['kichthuoc'] ?? $variant['kichthuoc'],
        'mausac' => $data['mausac'] ?? $variant['mausac'],
        'soluong' => $data['soluong'] ?? $variant['soluong'],
        'hinhanh' => $data['hinhanh'] ?? $variant['hinhanh']
    ];

    if ($bientheDAO->update($data['bienthe_id'], $updateData)) {
        $updated = $bientheDAO->getById($data['bienthe_id']);
        jsonResponse($updated, "Cập nhật thành công");
    } else {
        errorResponse("Không thể cập nhật", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
