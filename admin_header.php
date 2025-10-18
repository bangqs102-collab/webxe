<?php
// Luôn kiểm tra session an toàn ở đầu file

require_once 'admin_check.php'; // Bảo mật: Kiểm tra quyền admin
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Trang Quản Trị'; ?></title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #212529;
            color: #f8f9fa;
        }
        .table-dark {
            --bs-table-bg: #2b3035;
            border-color: #495057;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .card {
            background-color: #343a40;
            border-color: #495057;
        }
        .car-image {
            width: 120px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Bảng điều khiển Admin</h1>
        <div>
            <a href="dashboard.php" class="btn btn-info">Xem Thống Kê</a>
            <a href="orders.php" class="btn btn-warning">Quản Lý Đơn Hàng</a>
            <a href="../index.php" class="btn btn-light">Về trang chủ</a>
        </div>
    </div>