<?php
/**
 * Admin API: Chi tiết đơn hàng
 * GET /api/admin/orders/detail.php?id=...
 */

require_once __DIR__ . '/../../../config/response.php';
require_once __DIR__ . '/../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse("Method không được hỗ trợ", 405);
}

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($orderId <= 0) {
    errorResponse("Thiếu ID đơn hàng", 400);
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        errorResponse("Lỗi kết nối database", 500);
    }

    // Lấy thông tin đơn hàng
    $sql = "SELECT 
                dh.*,
                u.fullname AS ten_khachhang,
                u.phone AS sdt_khachhang,
                u.email AS email_khachhang,
                dc.tennguoinhan,
                dc.sodienthoai,
                dc.diachichitiet,
                dc.phuong,
                dc.tinh
            FROM donhang dh
            LEFT JOIN users u ON dh.user_id = u.user_id
            LEFT JOIN diachigiaohang dc ON dh.diachi_id = dc.diachi_id
            WHERE dh.donhang_id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $orderId]);
    $order = $stmt->fetch();

    if (!$order) {
        errorResponse("Không tìm thấy đơn hàng", 404);
    }

    // Lấy sản phẩm trong đơn
    $sqlItems = "SELECT 
                    ct.*,
                    sp.tensanpham,
                    sp.hinhanh,
                    sp.giaban,
                    sp.giakhuyenmai,
                    bt.kichthuoc,
                    bt.mausac
                FROM chitietdonhang ct
                INNER JOIN sanpham sp ON ct.sanpham_id = sp.sanpham_id
                LEFT JOIN bienthesp bt ON ct.bienthe_id = bt.bienthe_id
                WHERE ct.donhang_id = :id";

    $stmtItems = $conn->prepare($sqlItems);
    $stmtItems->execute([':id' => $orderId]);
    $items = $stmtItems->fetchAll();

    jsonResponse([
        'order' => $order,
        'items' => $items
    ]);

} catch (Exception $e) {
    errorResponse("Lỗi: " . $e->getMessage(), 500);
}
