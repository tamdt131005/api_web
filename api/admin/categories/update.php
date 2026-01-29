<?php
/**
 * Admin API: Sửa danh mục
 * PUT /api/admin/categories/update.php
 */

require_once __DIR__ . '/../../../config/response.php';
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['danhmuc_id', 'tendanhmuc']);

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        errorResponse("Lỗi kết nối database", 500);
    }

    $sql = "UPDATE danhmuc SET tendanhmuc = :ten, mota = :mota WHERE danhmuc_id = :id";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':ten' => $data['tendanhmuc'],
        ':mota' => $data['mota'] ?? '',
        ':id' => (int) $data['danhmuc_id']
    ]);

    if ($result) {
        jsonResponse(['danhmuc_id' => $data['danhmuc_id']], "Cập nhật danh mục thành công");
    } else {
        errorResponse("Không thể cập nhật", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi: " . $e->getMessage(), 500);
}
