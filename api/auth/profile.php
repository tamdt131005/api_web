<?php
/**
 * API Thông tin User / Cập nhật Profile
 * GET /api/auth/profile.php?user_id=X - Lấy thông tin
 * POST /api/auth/profile.php - Cập nhật thông tin
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/UserDAO.php';

try {
    $userDAO = new UserDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Lấy thông tin user
        $userId = $_GET['user_id'] ?? null;

        if (!$userId) {
            errorResponse("Thiếu user_id", 400);
        }

        $user = $userDAO->getById($userId);

        if ($user) {
            jsonResponse($user);
        } else {
            errorResponse("Không tìm thấy user", 404);
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Cập nhật thông tin
        $data = getJsonInput();
        validateRequired($data, ['user_id']);

        $userId = $data['user_id'];

        // Kiểm tra user tồn tại
        $existingUser = $userDAO->getById($userId);
        if (!$existingUser) {
            errorResponse("Không tìm thấy user", 404);
        }

        // Chuẩn bị data update
        $updateData = [
            'fullname' => $data['fullname'] ?? $existingUser['fullname'],
            'sex' => $data['sex'] ?? $existingUser['sex'],
            'ngaysinh' => $data['ngaysinh'] ?? $existingUser['ngaysinh'],
            'email' => $data['email'] ?? $existingUser['email'],
            'phone' => $data['phone'] ?? $existingUser['phone'],
            'avatar' => $data['avatar'] ?? $existingUser['avatar']
        ];

        if ($userDAO->update($userId, $updateData)) {
            $updatedUser = $userDAO->getById($userId);
            jsonResponse($updatedUser, "Cập nhật thành công");
        } else {
            errorResponse("Không thể cập nhật thông tin", 500);
        }

    } else {
        errorResponse("Method không được hỗ trợ", 405);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
