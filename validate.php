<?php
/**
 * validate.php - Cầu nối xác thực Key giữa App và Firebase Firestore
 * Bạn hãy thay đổi projectId bên dưới cho đúng với Firebase của bạn.
 */

// Cho phép truy cập từ mọi nguồn (tránh lỗi CORS nếu test trên trình duyệt)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// 1. CẤU HÌNH THÔNG TIN FIREBASE
// Lấy projectId từ file index.html của bạn (phần firebaseConfig)
$projectId = "locket-gold1"; 
$collectionName = "license"; // Tên collection chứa Key trong Firestore

// 2. LẤY KEY TỪ APP GỬI LÊN (Qua tham số ?key=...)
$inputKey = isset($_GET['key']) ? trim($_GET['key']) : '';

if (empty($inputKey)) {
    http_response_code(400);
    echo json_encode(["status" => 0, "message" => "Vui lòng cung cấp Key"]);
    exit;
}

/**
 * 3. GỌI FIRESTORE REST API
 * Logic: Kiểm tra xem Document ID (chính là Key) có tồn tại hay không.
 */
$url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collectionName}/{$inputKey}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Có thể bỏ qua kiểm tra SSL nếu host không hỗ trợ

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 4. TRẢ KẾT QUẢ VỀ CHO APP
// Nếu mã 200 nghĩa là tìm thấy Key (Document ID) trong hệ thống
if ($httpCode === 200) {
    // Key đúng
    echo "1"; 
} else if ($httpCode === 404) {
    // Key không tồn tại
    echo "0";
} else {
    // Các lỗi khác (Sai Project ID, lỗi mạng, v.v.)
    echo "0";
}
?>