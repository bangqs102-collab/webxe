<?php
session_start();

// Kiểm tra nếu người dùng chưa đăng nhập, HOẶC đã đăng nhập nhưng không phải admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Chuyển hướng họ về trang chủ
    header("Location: ../index.php");
    exit(); // Dừng thực thi ngay lập tức
}
?>