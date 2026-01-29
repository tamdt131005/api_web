<?php
/**
 * API Chi tiết / Cập nhật / Xóa Danh mục
 * GET /api/danhmuc/detail.php?id=X - Chi tiết
 * POST /api/danhmuc/detail.php - Cập nhật (với danhmuc_id trong body)
 * DELETE /api/danhmuc/detail.php?id=X - Xóa
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DanhmucDAO.php';

try {
    $danhmucDAO = new DanhmucDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $id = $_GET['id'] ?? null;
        $slug = $_GET['slug'] ?? null;

        if (!$id && !$slug) {
            errorResponse("Thiếu id hoặc slug", 400);
        }

        if ($id) {
            $category = $danhmucDAO->getById($id);
        } else {
            $category = $danhmucDAO->findBySlug($slug);
        }

        if ($category) {
            $category['so_sanpham'] = $danhmucDAO->countProducts($category['danhmuc_id']);
            jsonResponse($category);
        } else {
            errorResponse("Không tìm thấy danh mục", 404);
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Cập nhật
        $data = getJsonInput();
        validateRequired($data, ['danhmuc_id']);

        $category = $danhmucDAO->getById($data['danhmuc_id']);
        if (!$category) {
            errorResponse("Không tìm thấy danh mục", 404);
        }

        $updateData = [
            'tendanhmuc' => $data['tendanhmuc'] ?? $category['tendanhmuc'],
            'slug' => $data['slug'] ?? $category['slug'],
            'mota' => $data['mota'] ?? $category['mota']
        ];

        if ($danhmucDAO->update($data['danhmuc_id'], $updateData)) {
            $updated = $danhmucDAO->getById($data['danhmuc_id']);
            jsonResponse($updated, "Cập nhật thành công");
        } else {
            errorResponse("Không thể cập nhật", 500);
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            errorResponse("Thiếu id danh mục", 400);
        }

        if ($danhmucDAO->delete($id)) {
            jsonResponse(null, "Xóa thành công");
        } else {
            errorResponse("Không thể xóa", 500);
        }

    } else {
        errorResponse("Method không được hỗ trợ", 405);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}
