<?php
/**
 * API Đổi mật khẩu
 * POST /api/auth/change-password.php
 * Body: { "user_id": X, "old_password": "...", "new_password": "..." }
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/UserDAO.php';

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse("Method không được hỗ trợ", 405);
}

$data = getJsonInput();
validateRequired($data, ['user_id', 'old_password', 'new_password']);

try {
    $userDAO = new UserDAO();

    // Lấy thông tin user
    $user = $userDAO->getById($data['user_id']);
    if (!$user) {
        errorResponse("Không tìm thấy user", 404);
    }

    // Lấy user với password để verify
    $userWithPassword = $userDAO->findByUsername($user['username']);

    // Kiểm tra mật khẩu cũ
    if (!password_verify($data['old_password'], $userWithPassword['password'])) {
        errorResponse("Mật khẩu cũ không đúng", 400);
    }

    // Validate mật khẩu mới
    if (strlen($data['new_password']) < 6) {
        errorResponse("Mật khẩu mới phải có ít nhất 6 ký tự", 400);
    }

    // Đổi mật khẩu
    if ($userDAO->changePassword($data['user_id'], $data['new_password'])) {
        jsonResponse(null, "Đổi mật khẩu thành công");
    } else {
        errorResponse("Không thể đổi mật khẩu", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
