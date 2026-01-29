<?php
/**
 * Admin API: Danh sách sản phẩm
 * GET /api/admin/products/
 * Query: ?keyword=...&category_id=...
 */

require_once __DIR__ . '/../../../config/response.php';
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse("Method không được hỗ trợ", 405);
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        errorResponse("Lỗi kết nối database", 500);
    }

    $keyword = $_GET['keyword'] ?? null;
    $categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;

    $sql = "SELECT sp.*, dm.tendanhmuc 
            FROM sanpham sp
            LEFT JOIN danhmuc dm ON sp.danhmuc_id = dm.danhmuc_id
            WHERE 1=1";

    $params = [];

    if ($keyword) {
        $sql .= " AND (sp.tensanpham LIKE :kw OR sp.mota LIKE :kw2)";
        $params[':kw'] = "%$keyword%";
        $params[':kw2'] = "%$keyword%";
    }

    if ($categoryId) {
        $sql .= " AND sp.danhmuc_id = :cat";
        $params[':cat'] = $categoryId;
    }

    $sql .= " ORDER BY sp.sanpham_id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    jsonResponse(['products' => $products]);

} catch (Exception $e) {
    errorResponse("Lỗi: " . $e->getMessage(), 500);
}
