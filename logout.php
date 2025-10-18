<?php
session_start(); // Bắt đầu session
session_unset(); // Hủy tất cả các biến session
session_destroy(); // Hủy session
header("Location: index.php"); // Chuyển hướng về trang chủ
exit();
?>