<?php
/**
 * Response Helper - Trả về JSON response chuẩn
 */

// Cho phép CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * Trả về JSON response thành công
 * @param mixed $data Dữ liệu trả về
 * @param string $message Thông báo
 * @param int $code HTTP status code
 */
function jsonResponse($data, $message = "Thành công", $code = 200) {
    http_response_code($code);
    echo json_encode([
        "success" => true,
        "data" => $data,
        "message" => $message
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Trả về JSON response lỗi
 * @param string $message Thông báo lỗi
 * @param int $code HTTP status code
 */
function errorResponse($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        "success" => false,
        "error" => $message,
        "code" => $code
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Lấy dữ liệu JSON từ request body
 * @return array
 */
function getJsonInput() {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    return $data ?? [];
}

/**
 * Kiểm tra các field bắt buộc
 * @param array $data Dữ liệu cần kiểm tra
 * @param array $required Danh sách field bắt buộc
 * @return bool
 */
function validateRequired($data, $required) {
    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            errorResponse("Thiếu trường bắt buộc: {$field}", 400);
        }
    }
    return true;
}
