<?php
/**
 * Admin API: Cập nhật trạng thái đơn hàng
 * PUT /api/admin/orders/update-status.php
 * Body: { "donhang_id": 1, "trangthai": "daxacnhan" }
 */

require_once __DIR__ . '/../../../config/response.php';
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['donhang_id', 'trangthai']);

$validStatuses = ['choxacnhan', 'daxacnhan', 'dangxuly', 'danggiao', 'dagiao', 'dahuy'];
if (!in_array($data['trangthai'], $validStatuses)) {
    errorResponse("Trạng thái không hợp lệ", 400);
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        errorResponse("Lỗi kết nối database", 500);
    }

    $sql = "UPDATE donhang SET trangthai = :status, ngaycapnhat = NOW() WHERE donhang_id = :id";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':status' => $data['trangthai'],
        ':id' => (int) $data['donhang_id']
    ]);

    if ($result) {
        jsonResponse(['donhang_id' => $data['donhang_id']], "Cập nhật trạng thái thành công");
    } else {
        errorResponse("Không thể cập nhật", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi: " . $e->getMessage(), 500);
}
