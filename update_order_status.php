<?php
// php/update_order_status.php
session_start();
require_once '../includes/db_connect.php';

// 1. Kiểm tra bảo mật
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['action']) || $_GET['action'] !== 'cancel' || !isset($_GET['id'])) {
    header("Location: ../my_orders.php");
    exit();
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
$new_status = 'Đã hủy';
$allowed_status_to_cancel = 'Chờ xác nhận';

// 2. Cập nhật trạng thái trong CSDL, nhưng phải đảm bảo đúng chủ và đúng trạng thái
$sql = "UPDATE orders SET status = ? 
        WHERE id = ? 
        AND user_id = ? 
        AND status = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("siis", $new_status, $order_id, $user_id, $allowed_status_to_cancel);
$stmt->execute();

// 3. Chuyển hướng về trang lịch sử đơn hàng
header("Location: ../my_orders.php?status=cancelled");
exit();
?>