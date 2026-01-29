<?php
/**
 * Admin API: Lấy danh sách đơn hàng
 * GET /api/admin/orders/
 * Query params: ?status=choxacnhan&keyword=...
 */

require_once __DIR__ . '/../../../config/response.php';
require_once __DIR__ . '/../../../config/database.php';

// Chỉ chấp nhận GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse("Method không được hỗ trợ", 405);
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        errorResponse("Lỗi kết nối database", 500);
    }

    // Lấy params
    $status = $_GET['status'] ?? null;
    $keyword = $_GET['keyword'] ?? null;

    $sql = "SELECT 
                dh.donhang_id,
                dh.user_id,
                dh.ghichu,
                dh.trangthai,
                dh.phuongthucthanhtoan,
                dh.trangthaithanhtoan,
                dh.tongtienhang,
                dh.phivanchuyen,
                dh.tongthanhtoan,
                dh.ngaytao,
                dh.ngaycapnhat,
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
            WHERE 1=1";

    $params = [];

    // Lọc theo trạng thái
    if ($status && $status !== 'all') {
        $sql .= " AND dh.trangthai = :status";
        $params[':status'] = $status;
    }

    // Tìm kiếm
    if ($keyword) {
        $sql .= " AND (dh.donhang_id LIKE :kw1 OR u.fullname LIKE :kw2 OR u.phone LIKE :kw3)";
        $params[':kw1'] = "%$keyword%";
        $params[':kw2'] = "%$keyword%";
        $params[':kw3'] = "%$keyword%";
    }

    $sql .= " ORDER BY dh.ngaytao DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

    // Thống kê
    $sqlStats = "SELECT trangthai, COUNT(*) as soluong FROM donhang GROUP BY trangthai";
    $stmtStats = $conn->query($sqlStats);
    $statsRaw = $stmtStats->fetchAll();

    $stats = [
        'tatca' => 0,
        'choxacnhan' => 0,
        'daxacnhan' => 0,
        'dangxuly' => 0,
        'danggiao' => 0,
        'dagiao' => 0,
        'dahuy' => 0
    ];

    foreach ($statsRaw as $row) {
        $stats[$row['trangthai']] = (int) $row['soluong'];
        $stats['tatca'] += (int) $row['soluong'];
    }

    jsonResponse([
        'orders' => $orders,
        'stats' => $stats
    ]);

} catch (Exception $e) {
    errorResponse("Lỗi: " . $e->getMessage(), 500);
}
