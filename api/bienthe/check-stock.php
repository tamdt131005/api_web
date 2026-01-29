<?php
/**
 * API Kiểm tra tồn kho biến thể
 * POST /api/bienthe/check-stock.php
 * Body: { "bienthe_id": X, "soluong": Y }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/BientheDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['bienthe_id', 'soluong']);

try {
    $bientheDAO = new BientheDAO();

    $variant = $bientheDAO->getById($data['bienthe_id']);
    if (!$variant) {
        errorResponse("Không tìm thấy biến thể", 404);
    }

    $available = $bientheDAO->checkStock($data['bienthe_id'], $data['soluong']);

    jsonResponse([
        'available' => $available,
        'tonkho' => $variant['soluong'],
        'yeu_cau' => $data['soluong']
    ]);

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
