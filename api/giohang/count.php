<?php
/**
 * API Đếm số SP trong giỏ
 * GET /api/giohang/count.php?user_id=X
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/GiohangDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse("Method không được hỗ trợ", 405);
}

$userId = $_GET['user_id'] ?? null;

if (!$userId) {
    errorResponse("Thiếu user_id", 400);
}

try {
    $giohangDAO = new GiohangDAO();

    $count = $giohangDAO->countItems($userId);
    $sumQuantity = $giohangDAO->sumQuantity($userId);

    jsonResponse([
        'items_count' => $count,
        'total_quantity' => $sumQuantity
    ]);

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
