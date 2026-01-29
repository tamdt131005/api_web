<?php
/**
 * API Chi tiết sản phẩm
 * GET /api/sanpham/detail.php?id=X
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/SanphamDAO.php';
require_once __DIR__ . '/../../dao/BientheDAO.php';
require_once __DIR__ . '/../../dao/DanhgiaDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse("Method không được hỗ trợ", 405);
}

$id = $_GET['id'] ?? null;
$slug = $_GET['slug'] ?? null;

if (!$id && !$slug) {
    errorResponse("Thiếu id hoặc slug sản phẩm", 400);
}

try {
    $sanphamDAO = new SanphamDAO();
    $bientheDAO = new BientheDAO();
    $danhgiaDAO = new DanhgiaDAO();

    // Lấy sản phẩm
    if ($id) {
        $product = $sanphamDAO->getById($id);
    } else {
        $product = $sanphamDAO->findBySlug($slug);
    }

    if (!$product) {
        errorResponse("Không tìm thấy sản phẩm", 404);
    }

    // Lấy biến thể (size, màu)
    $variants = $bientheDAO->findBySanpham($product['sanpham_id']);
    $sizes = $bientheDAO->getSizes($product['sanpham_id']);
    $colors = $bientheDAO->getColors($product['sanpham_id']);

    // Lấy đánh giá
    $reviews = $danhgiaDAO->findBySanpham($product['sanpham_id']);
    $rating = $danhgiaDAO->getAverageRating($product['sanpham_id']);

    jsonResponse([
        'product' => $product,
        'variants' => $variants,
        'sizes' => $sizes,
        'colors' => $colors,
        'reviews' => $reviews,
        'rating' => $rating
    ]);

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
