<?php
// Thay đổi các thông tin sau cho phù hợp với cấu hình của bạn
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_db";


// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// Thiết lập charset để hiển thị tiếng Việt
$conn->set_charset("utf8mb4");
?>