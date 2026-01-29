<?php
/**
 * API Danh mục - Danh sách & Tạo mới
 * GET /api/danhmuc/ - Danh sách
 * POST /api/danhmuc/ - Tạo mới
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/DanhmucDAO.php';

try {
    $danhmucDAO = new DanhmucDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $categories = $danhmucDAO->getAll();

        // Thêm số lượng sản phẩm trong mỗi danh mục
        foreach ($categories as &$cat) {
            $cat['so_sanpham'] = $danhmucDAO->countProducts($cat['danhmuc_id']);
        }

        jsonResponse($categories);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = getJsonInput();
        validateRequired($data, ['tendanhmuc']);

        // Tạo slug nếu không có
        if (!isset($data['slug'])) {
            $data['slug'] = createSlug($data['tendanhmuc']);
        }

        $catId = $danhmucDAO->create($data);

        if ($catId) {
            $newCat = $danhmucDAO->getById($catId);
            jsonResponse($newCat, "Tạo danh mục thành công", 201);
        } else {
            errorResponse("Không thể tạo danh mục", 500);
        }

    } else {
        errorResponse("Method không được hỗ trợ", 405);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}

function createSlug($text)
{
    $slug = removeVietnameseAccents($text);
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    return trim($slug, '-');
}

function removeVietnameseAccents($str)
{
    $patterns = [
        '/[àáạảãâầấậẩẫăằắặẳẵ]/u' => 'a',
        '/[èéẹẻẽêềếệểễ]/u' => 'e',
        '/[ìíịỉĩ]/u' => 'i',
        '/[òóọỏõôồốộổỗơờớợởỡ]/u' => 'o',
        '/[ùúụủũưừứựửữ]/u' => 'u',
        '/[ỳýỵỷỹ]/u' => 'y',
        '/đ/u' => 'd'
    ];
    foreach ($patterns as $pattern => $replacement) {
        $str = preg_replace($pattern, $replacement, $str);
    }
    return $str;
}
