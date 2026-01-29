<?php
/**
 * API Cập nhật trạng thái đơn hàng (Admin)
 * POST /api/donhang/update-status.php
 * Body: { "donhang_id": X, "trangthai": "..." }
 * 
 * Trạng thái: choxacnhan, daxacnhan, dangxuly, danggiao, dagiao, dahuy
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DonhangDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['donhang_id', 'trangthai']);

// Các trạng thái hợp lệ
$validStatus = ['choxacnhan', 'daxacnhan', 'dangxuly', 'danggiao', 'dagiao', 'dahuy'];

if (!in_array($data['trangthai'], $validStatus)) {
    errorResponse("Trạng thái không hợp lệ", 400);
}

try {
    $donhangDAO = new DonhangDAO();

    $order = $donhangDAO->getById($data['donhang_id']);
    if (!$order) {
        errorResponse("Không tìm thấy đơn hàng", 404);
    }

    $lydo = $data['lydo'] ?? null;

    if ($donhangDAO->updateStatus($data['donhang_id'], $data['trangthai'], $lydo)) {
        $updatedOrder = $donhangDAO->getById($data['donhang_id']);
        jsonResponse($updatedOrder, "Cập nhật trạng thái thành công");
    } else {
        errorResponse("Không thể cập nhật", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
