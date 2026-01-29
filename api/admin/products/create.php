<?php
/**
 * Admin API: Thêm sản phẩm
 * POST /api/admin/products/create.php
 */

require_once __DIR__ . '/../../../config/response.php';
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['tensanpham', 'giaban', 'danhmuc_id']);

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        errorResponse("Lỗi kết nối database", 500);
    }

    $sql = "INSERT INTO sanpham (tensanpham, mota, giaban, giakhuyenmai, hinhanh, danhmuc_id, soluong, trangthai)
            VALUES (:ten, :mota, :gia, :giakm, :hinh, :dm, :sl, :tt)";

    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':ten' => $data['tensanpham'],
        ':mota' => $data['mota'] ?? '',
        ':gia' => (float) $data['giaban'],
        ':giakm' => isset($data['giakhuyenmai']) ? (float) $data['giakhuyenmai'] : null,
        ':hinh' => $data['hinhanh'] ?? null,
        ':dm' => (int) $data['danhmuc_id'],
        ':sl' => (int) ($data['soluong'] ?? 0),
        ':tt' => $data['trangthai'] ?? 'conhang'
    ]);

    if ($result) {
        $newId = $conn->lastInsertId();
        jsonResponse(['sanpham_id' => $newId], "Thêm sản phẩm thành công", 201);
    } else {
        errorResponse("Không thể thêm sản phẩm", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi: " . $e->getMessage(), 500);
}
