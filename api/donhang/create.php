<?php
/**
 * API Tạo đơn hàng từ giỏ hàng (CHECKOUT)
 * POST /api/donhang/create.php
 * Body: {
 *   "user_id": X,
 *   "diachi_id": X,
 *   "ghichu": "...",
 *   "phuongthucthanhtoan": "tienmat" | "chuyenkhoan",
 *   "phivanchuyen": 30000
 * }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DonhangDAO.php';
require_once __DIR__ . '/../../dao/ChitietdonhangDAO.php';
require_once __DIR__ . '/../../dao/GiohangDAO.php';
require_once __DIR__ . '/../../dao/BientheDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['user_id', 'diachi_id', 'phuongthucthanhtoan']);

try {
    $giohangDAO = new GiohangDAO();
    $donhangDAO = new DonhangDAO();
    $chitietDAO = new ChitietdonhangDAO();
    $bientheDAO = new BientheDAO();

    // Lấy giỏ hàng
    $cart = $giohangDAO->getCartWithTotal($data['user_id']);

    if (empty($cart['items'])) {
        errorResponse("Giỏ hàng trống, không thể đặt hàng", 400);
    }

    // Kiểm tra tồn kho từng item
    foreach ($cart['items'] as $item) {
        if (!$bientheDAO->checkStock($item['bienthe_id'], $item['soluong'])) {
            errorResponse("Sản phẩm '{$item['tensanpham']}' không đủ số lượng trong kho", 400);
        }
    }

    // Tính tổng tiền
    $tongtienhang = $cart['tongtienhang'];
    $phivanchuyen = $data['phivanchuyen'] ?? 30000;
    $tongthanhtoan = $tongtienhang + $phivanchuyen;

    // Tạo đơn hàng
    $orderData = [
        'user_id' => $data['user_id'],
        'diachi_id' => $data['diachi_id'],
        'ghichu' => $data['ghichu'] ?? '',
        'phuongthucthanhtoan' => $data['phuongthucthanhtoan'],
        'tongtienhang' => $tongtienhang,
        'phivanchuyen' => $phivanchuyen,
        'tongthanhtoan' => $tongthanhtoan
    ];

    $donhangId = $donhangDAO->create($orderData);

    if (!$donhangId) {
        errorResponse("Không thể tạo đơn hàng", 500);
    }

    // Tạo chi tiết đơn hàng
    $chitietDAO->createFromCart($donhangId, $cart['items']);

    // Trừ tồn kho
    foreach ($cart['items'] as $item) {
        $bientheDAO->decreaseStock($item['bienthe_id'], $item['soluong']);
    }

    // Xóa giỏ hàng
    $giohangDAO->clearCart($data['user_id']);

    // Lấy thông tin đơn hàng vừa tạo
    $order = $donhangDAO->getById($donhangId);
    $orderDetails = $chitietDAO->findByDonhang($donhangId);

    jsonResponse([
        'order' => $order,
        'details' => $orderDetails
    ], "Đặt hàng thành công", 201);

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
