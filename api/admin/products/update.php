<?php
/**
 * Admin API: Sửa sản phẩm
 * PUT /api/admin/products/update.php
 */

require_once __DIR__ . '/../../../config/response.php';
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['sanpham_id', 'tensanpham', 'giaban']);

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        errorResponse("Lỗi kết nối database", 500);
    }

    $sql = "UPDATE sanpham SET 
                tensanpham = :ten,
                mota = :mota,
                giaban = :gia,
                giakhuyenmai = :giakm,
                hinhanh = :hinh,
                danhmuc_id = :dm,
                soluong = :sl,
                trangthai = :tt
            WHERE sanpham_id = :id";

    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':ten' => $data['tensanpham'],
        ':mota' => $data['mota'] ?? '',
        ':gia' => (float) $data['giaban'],
        ':giakm' => isset($data['giakhuyenmai']) ? (float) $data['giakhuyenmai'] : null,
        ':hinh' => $data['hinhanh'] ?? null,
        ':dm' => (int) ($data['danhmuc_id'] ?? 0),
        ':sl' => (int) ($data['soluong'] ?? 0),
        ':tt' => $data['trangthai'] ?? 'conhang',
        ':id' => (int) $data['sanpham_id']
    ]);

    if ($result) {
        jsonResponse(['sanpham_id' => $data['sanpham_id']], "Cập nhật sản phẩm thành công");
    } else {
        errorResponse("Không thể cập nhật", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi: " . $e->getMessage(), 500);
}
