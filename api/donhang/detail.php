<?php
/**
 * API Chi tiết đơn hàng
 * GET /api/donhang/detail.php?id=X
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DonhangDAO.php';
require_once __DIR__ . '/../../dao/ChitietdonhangDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse("Method không được hỗ trợ", 405);
}

$id = $_GET['id'] ?? null;

if (!$id) {
    errorResponse("Thiếu id đơn hàng", 400);
}

try {
    $donhangDAO = new DonhangDAO();
    $chitietDAO = new ChitietdonhangDAO();

    $order = $donhangDAO->getById($id);

    if (!$order) {
        errorResponse("Không tìm thấy đơn hàng", 404);
    }

    $details = $chitietDAO->findByDonhang($id);

    jsonResponse([
        'order' => $order,
        'details' => $details
    ]);

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
