<?php
/**
 * API Đăng ký
 * POST /api/auth/register.php
 * Body: { "username": "...", "password": "...", "email": "...", "fullname": "...", ... }
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
validateRequired($data, ['username', 'password', 'email', 'fullname']);

try {
    $userDAO = new UserDAO();

    // Kiểm tra username đã tồn tại
    if ($userDAO->findByUsername($data['username'])) {
        errorResponse("Tên đăng nhập đã được sử dụng", 400);
    }

    // Kiểm tra email đã tồn tại
    if ($userDAO->findByEmail($data['email'])) {
        errorResponse("Email đã được sử dụng", 400);
    }

    // Chuẩn bị data
    $userData = [
        'username' => $data['username'],
        'password' => $data['password'],
        'email' => $data['email'],
        'fullname' => $data['fullname'],
        'sex' => $data['sex'] ?? null,
        'ngaysinh' => $data['ngaysinh'] ?? null,
        'phone' => $data['phone'] ?? null,
        'avatar' => $data['avatar'] ?? null,
        'role' => 'user',
        'status' => 'Y'
    ];

    // Tạo user
    $userId = $userDAO->create($userData);

    if ($userId) {
        $newUser = $userDAO->getById($userId);
        jsonResponse([
            'user' => $newUser
        ], "Đăng ký thành công", 201);
    } else {
        errorResponse("Không thể tạo tài khoản", 500);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
