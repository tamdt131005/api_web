<?php
/**
 * API Chi tiết / Cập nhật / Xóa địa chỉ
 * GET /api/diachi/detail.php?id=X - Chi tiết
 * POST /api/diachi/detail.php - Cập nhật
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DiachigiaohangDAO.php';

try {
    $diachiDAO = new DiachigiaohangDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            errorResponse("Thiếu id địa chỉ", 400);
        }

        $address = $diachiDAO->getById($id);

        if ($address) {
            jsonResponse($address);
        } else {
            errorResponse("Không tìm thấy địa chỉ", 404);
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = getJsonInput();
        validateRequired($data, ['diachi_id']);

        $address = $diachiDAO->getById($data['diachi_id']);
        if (!$address) {
            errorResponse("Không tìm thấy địa chỉ", 404);
        }

        $updateData = [
            'tennguoinhan' => $data['tennguoinhan'] ?? $address['tennguoinhan'],
            'sodienthoai' => $data['sodienthoai'] ?? $address['sodienthoai'],
            'diachichitiet' => $data['diachichitiet'] ?? $address['diachichitiet'],
            'phuong' => $data['phuong'] ?? $address['phuong'],
            'tinh' => $data['tinh'] ?? $address['tinh']
        ];

        if ($diachiDAO->update($data['diachi_id'], $updateData)) {
            $updated = $diachiDAO->getById($data['diachi_id']);
            jsonResponse($updated, "Cập nhật thành công");
        } else {
            errorResponse("Không thể cập nhật", 500);
        }

    } else {
        errorResponse("Method không được hỗ trợ", 405);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
