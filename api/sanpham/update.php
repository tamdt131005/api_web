<?php
/**
 * API Cập nhật sản phẩm
 * POST /api/sanpham/update.php
 * Body: { "sanpham_id": X, ... }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/SanphamDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['sanpham_id']);

try {
    $sanphamDAO = new SanphamDAO();

    // Kiểm tra tồn tại
    $product = $sanphamDAO->getById($data['sanpham_id']);
    if (!$product) {
        errorResponse("Không tìm thấy sản phẩm", 404);
    }

    // Chuẩn bị data update
    $updateData = [
        'danhmuc_id' => $data['danhmuc_id'] ?? $product['danhmuc_id'],
        'tensanpham' => $data['tensanpham'] ?? $product['tensanpham'],
        'slug' => $data['slug'] ?? $product['slug'],
        'mota' => $data['mota'] ?? $product['mota'],
        'giaban' => $data['giaban'] ?? $product['giaban'],
        'giakhuyenmai' => $data['giakhuyenmai'] ?? $product['giakhuyenmai'],
        'soluong' => $data['soluong'] ?? $product['soluong'],
        'hinhanh' => $data['hinhanh'] ?? $product['hinhanh']
    ];

    if ($sanphamDAO->update($data['sanpham_id'], $updateData)) {
        $updatedProduct = $sanphamDAO->getById($data['sanpham_id']);
        jsonResponse($updatedProduct, "Cập nhật thành công");
    } else {
        errorResponse("Không thể cập nhật sản phẩm", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
