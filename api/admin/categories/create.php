<?php
/**
 * Admin API: Thêm danh mục
 * POST /api/admin/categories/create.php
 */

require_once __DIR__ . '/../../../config/response.php';
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['tendanhmuc']);

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        errorResponse("Lỗi kết nối database", 500);
    }

    $sql = "INSERT INTO danhmuc (tendanhmuc, mota) VALUES (:ten, :mota)";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':ten' => $data['tendanhmuc'],
        ':mota' => $data['mota'] ?? ''
    ]);

    if ($result) {
        $newId = $conn->lastInsertId();
        jsonResponse(['danhmuc_id' => $newId], "Thêm danh mục thành công", 201);
    } else {
        errorResponse("Không thể thêm danh mục", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi: " . $e->getMessage(), 500);
}
