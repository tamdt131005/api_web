<?php
/**
 * API Đơn hàng - Danh sách
 * GET /api/donhang/?user_id=X - Đơn hàng của user
 * GET /api/donhang/ - Tất cả đơn hàng (admin)
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DonhangDAO.php';
require_once __DIR__ . '/../../dao/ChitietdonhangDAO.php';

try {
    $donhangDAO = new DonhangDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $userId = $_GET['user_id'] ?? null;
        $status = $_GET['status'] ?? null;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

        if ($userId) {
            // Đơn hàng của user
            $orders = $donhangDAO->findByUser($userId, $page, $limit);
        } elseif ($status) {
            // Lọc theo trạng thái
            $orders = $donhangDAO->findByStatus($status, $page, $limit);
        } else {
            // Tất cả (admin)
            $orders = $donhangDAO->getAll($page, $limit);
        }

        jsonResponse([
            'orders' => $orders,
            'pagination' => [
                'page' => $page,
                'limit' => $limit
            ]
        ]);

    } else {
        errorResponse("Method không được hỗ trợ", 405);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
