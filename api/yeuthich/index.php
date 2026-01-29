<?php
/**
 * API Sản phẩm yêu thích
 * GET /api/yeuthich/?user_id=X - Danh sách SP yêu thích
 * POST /api/yeuthich/ - Toggle yêu thích (thêm/xóa)
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/YeuthichDAO.php';

try {
    $yeuthichDAO = new YeuthichDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $userId = $_GET['user_id'] ?? null;

        if (!$userId) {
            errorResponse("Thiếu user_id", 400);
        }

        $favorites = $yeuthichDAO->findByUser($userId);
        $count = $yeuthichDAO->countByUser($userId);

        jsonResponse([
            'favorites' => $favorites,
            'total' => $count
        ]);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = getJsonInput();
        validateRequired($data, ['user_id', 'sanpham_id']);

        $result = $yeuthichDAO->toggle($data['user_id'], $data['sanpham_id']);

        jsonResponse($result, $result['action'] === 'added' ?
            "Đã thêm vào yêu thích" : "Đã xóa khỏi yêu thích");

    } else {
        errorResponse("Method không được hỗ trợ", 405);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
