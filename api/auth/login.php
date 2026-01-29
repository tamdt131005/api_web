<?php
/**
 * API Đăng nhập
 * POST /api/auth/login.php
 * Body: { "username": "...", "password": "..." }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/UserDAO.php';

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

// Lấy data từ request
$data = getJsonInput();

// Validate required fields
validateRequired($data, ['username', 'password']);

try {
    $userDAO = new UserDAO();

    // Xác thực
    $user = $userDAO->verifyPassword($data['username'], $data['password']);

    if ($user) {
        jsonResponse([
            'user' => $user
        ], "Đăng nhập thành công");
    } else {
        errorResponse("Tên đăng nhập hoặc mật khẩu không đúng", 401);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
