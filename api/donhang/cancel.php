<?php
/**
 * API Hủy đơn hàng
 * POST /api/donhang/cancel.php
 * Body: { "donhang_id": X, "lydo": "..." }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DonhangDAO.php';
require_once __DIR__ . '/../../dao/ChitietdonhangDAO.php';
require_once __DIR__ . '/../../dao/BientheDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['donhang_id', 'lydo']);

try {
    $donhangDAO = new DonhangDAO();
    $chitietDAO = new ChitietdonhangDAO();
    $bientheDAO = new BientheDAO();

    // Kiểm tra đơn hàng
    $order = $donhangDAO->getById($data['donhang_id']);

    if (!$order) {
        errorResponse("Không tìm thấy đơn hàng", 404);
    }

    // Chỉ cho phép hủy ở trạng thái chờ xác nhận hoặc đã xác nhận
    $allowedStatus = ['choxacnhan', 'daxacnhan'];
    if (!in_array($order['trangthai'], $allowedStatus)) {
        errorResponse("Không thể hủy đơn hàng ở trạng thái này", 400);
    }

    // Hoàn lại tồn kho
    $details = $chitietDAO->findByDonhang($data['donhang_id']);
    foreach ($details as $item) {
        $bientheDAO->increaseStock($item['bienthe_id'], $item['soluong']);
    }

    // Hủy đơn
    if ($donhangDAO->cancel($data['donhang_id'], $data['lydo'])) {
        $updatedOrder = $donhangDAO->getById($data['donhang_id']);
        jsonResponse($updatedOrder, "Hủy đơn hàng thành công");
    } else {
        errorResponse("Không thể hủy đơn hàng", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
