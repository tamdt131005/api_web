<?php
/**
 * API Đánh giá sản phẩm - Danh sách & Tạo mới
 * GET /api/danhgia/?sanpham_id=X - Đánh giá của SP
 * GET /api/danhgia/?user_id=X - Đánh giá của user
 * POST /api/danhgia/ - Tạo đánh giá mới
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DanhgiaDAO.php';

try {
    $danhgiaDAO = new DanhgiaDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sanphamId = $_GET['sanpham_id'] ?? null;
        $userId = $_GET['user_id'] ?? null;

        if ($sanphamId) {
            $reviews = $danhgiaDAO->findBySanpham($sanphamId);
            $rating = $danhgiaDAO->getAverageRating($sanphamId);
            jsonResponse([
                'reviews' => $reviews,
                'rating' => $rating
            ]);
        } elseif ($userId) {
            $reviews = $danhgiaDAO->findByUser($userId);
            jsonResponse($reviews);
        } else {
            errorResponse("Thiếu sanpham_id hoặc user_id", 400);
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = getJsonInput();
        validateRequired($data, ['sanpham_id', 'user_id', 'danhgia', 'tieude', 'mota']);

        // Validate điểm đánh giá 1-5
        if ($data['danhgia'] < 1 || $data['danhgia'] > 5) {
            errorResponse("Điểm đánh giá phải từ 1 đến 5", 400);
        }

        // Kiểm tra đã đánh giá chưa
        $existing = $danhgiaDAO->checkExists($data['user_id'], $data['sanpham_id']);
        if ($existing) {
            errorResponse("Bạn đã đánh giá sản phẩm này rồi", 400);
        }

        $reviewId = $danhgiaDAO->create($data);

        if ($reviewId) {
            $newReview = $danhgiaDAO->getById($reviewId);
            jsonResponse($newReview, "Đánh giá thành công", 201);
        } else {
            errorResponse("Không thể tạo đánh giá", 500);
        }

    } else {
        errorResponse("Method không được hỗ trợ", 405);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
