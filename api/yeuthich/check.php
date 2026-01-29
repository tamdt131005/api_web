<?php
/**
 * API Kiểm tra SP đã yêu thích chưa
 * POST /api/yeuthich/check.php
 * Body: { "user_id": X, "sanpham_id": X }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/YeuthichDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['user_id', 'sanpham_id']);

try {
    $yeuthichDAO = new YeuthichDAO();

    $liked = $yeuthichDAO->checkExists($data['user_id'], $data['sanpham_id']);

    jsonResponse([
        'liked' => $liked
    ]);

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
