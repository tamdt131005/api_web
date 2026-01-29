<?php
/**
 * API Biến thể sản phẩm
 * GET /api/bienthe/?sanpham_id=X - Lấy biến thể của SP
 * POST /api/bienthe/ - Tạo biến thể mới
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/BientheDAO.php';

try {
    $bientheDAO = new BientheDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sanphamId = $_GET['sanpham_id'] ?? null;

        if ($sanphamId) {
            $variants = $bientheDAO->findBySanpham($sanphamId);
            $sizes = $bientheDAO->getSizes($sanphamId);
            $colors = $bientheDAO->getColors($sanphamId);

            jsonResponse([
                'variants' => $variants,
                'sizes' => $sizes,
                'colors' => $colors
            ]);
        } else {
            $variants = $bientheDAO->getAll();
            jsonResponse($variants);
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = getJsonInput();
        validateRequired($data, ['sanpham_id', 'kichthuoc', 'mausac', 'soluong']);

        $variantId = $bientheDAO->create($data);

        if ($variantId) {
            $newVariant = $bientheDAO->getById($variantId);
            jsonResponse($newVariant, "Tạo biến thể thành công", 201);
        } else {
            errorResponse("Không thể tạo biến thể", 500);
        }

    } else {
        errorResponse("Method không được hỗ trợ", 405);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
