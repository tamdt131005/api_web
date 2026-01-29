<?php
/**
 * API Sản phẩm - Danh sách & Tạo mới
 * GET /api/sanpham/ - Danh sách (có phân trang, filter)
 * POST /api/sanpham/ - Tạo mới (admin)
 */

require_once __DIR__ . '/../../config/response.php';
require_once __DIR__ . '/../../dao/SanphamDAO.php';
require_once __DIR__ . '/../../dao/BientheDAO.php';
require_once __DIR__ . '/../../dao/DanhgiaDAO.php';

try {
    $sanphamDAO = new SanphamDAO();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

        // Lọc theo danh mục
        if (isset($_GET['danhmuc_id'])) {
            $products = $sanphamDAO->findByDanhmuc($_GET['danhmuc_id'], $page, $limit);
        }
        // Tìm kiếm
        elseif (isset($_GET['search'])) {
            $products = $sanphamDAO->search($_GET['search'], $page, $limit);
        }
        // Sản phẩm mới
        elseif (isset($_GET['latest'])) {
            $products = $sanphamDAO->getLatest($limit);
        }
        // Sản phẩm khuyến mãi
        elseif (isset($_GET['sale'])) {
            $products = $sanphamDAO->getSale($limit);
        }
        // Tất cả
        else {
            $products = $sanphamDAO->getAll($page, $limit);
        }

        $total = $sanphamDAO->countAll();

        jsonResponse([
            'products' => $products,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ]);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Tạo sản phẩm mới
        $data = getJsonInput();
        validateRequired($data, ['danhmuc_id', 'tensanpham', 'giaban', 'soluong', 'hinhanh']);

        // Tạo slug từ tên nếu không có
        if (!isset($data['slug'])) {
            $data['slug'] = createSlug($data['tensanpham']);
        }

        $productId = $sanphamDAO->create($data);

        if ($productId) {
            $newProduct = $sanphamDAO->getById($productId);
            jsonResponse($newProduct, "Tạo sản phẩm thành công", 201);
        } else {
            errorResponse("Không thể tạo sản phẩm", 500);
        }

    } else {
        errorResponse("Method không được hỗ trợ", 405);
    }

} catch (Exception $e) {
    errorResponse("Lỗi hệ thống: " . $e->getMessage(), 500);
}

/**
 * Tạo slug từ text
 */
function createSlug($text)
{
    // Chuyển sang không dấu
    $slug = removeVietnameseAccents($text);
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
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
        '/đ/u' => 'd',
        '/[ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴ]/u' => 'A',
        '/[ÈÉẸẺẼÊỀẾỆỂỄ]/u' => 'E',
        '/[ÌÍỊỈĨ]/u' => 'I',
        '/[ÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠ]/u' => 'O',
        '/[ÙÚỤỦŨƯỪỨỰỬỮ]/u' => 'U',
        '/[ỲÝỴỶỸ]/u' => 'Y',
        '/Đ/u' => 'D'
    ];

    foreach ($patterns as $pattern => $replacement) {
        $str = preg_replace($pattern, $replacement, $str);
    }
    return $str;
}
