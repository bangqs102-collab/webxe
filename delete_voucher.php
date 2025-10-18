<?php
// admin/delete_voucher.php
require 'admin_check.php';
require '../includes/db_connect.php';

$voucher_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($voucher_id > 0) {
    $sql = "DELETE FROM vouchers WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $voucher_id);
    mysqli_stmt_execute($stmt);
}

header("Location: manage_vouchers.php");
exit();