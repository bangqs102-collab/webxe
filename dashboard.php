<?php
require_once 'admin_check.php';
require_once '../includes/db_connect.php';
// Lấy dữ liệu 5 đơn hàng mới nhất
$sql_recent_orders = "SELECT o.id, u.username, o.total_price, o.status, o.order_date
                      FROM orders o
                      JOIN users u ON o.user_id = u.id
                      ORDER BY o.order_date DESC
                      LIMIT 5";
$result_recent_orders = $conn->query($sql_recent_orders);
$recent_orders = $result_recent_orders->fetch_all(MYSQLI_ASSOC);

// Thống kê doanh thu 7 ngày gần nhất
$revenue_by_day = [];
$sql_chart = "SELECT DATE(order_date) as sale_date, SUM(total_price) as daily_revenue
              FROM orders
              WHERE status = 'Completed' AND order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
              GROUP BY DATE(order_date)
              ORDER BY sale_date ASC";
$result_chart = $conn->query($sql_chart);
while ($row = $result_chart->fetch_assoc()) {
    $revenue_by_day[] = $row;
}

// Chuyển dữ liệu sang dạng JSON để JavaScript có thể đọc
$chart_labels = json_encode(array_column($revenue_by_day, 'sale_date'));
$chart_data = json_encode(array_column($revenue_by_day, 'daily_revenue'));

// Thống kê Doanh thu
$result_revenue = $conn->query("SELECT SUM(total_price) as total_revenue FROM orders WHERE status = 'Completed'");
$total_revenue = $result_revenue->fetch_assoc()['total_revenue'] ?? 0;

// Thống kê Tổng số đơn hàng
$result_orders = $conn->query("SELECT COUNT(id) as total_orders FROM orders");
$total_orders = $result_orders->fetch_assoc()['total_orders'] ?? 0;

// Thống kê Tổng số khách hàng
$result_users = $conn->query("SELECT COUNT(id) as total_users FROM users");
$total_users = $result_users->fetch_assoc()['total_users'] ?? 0;
// Thống kê Xe bán chạy nhất
$result_top_car = $conn->query("
    SELECT c.brand, c.model, COUNT(oi.product_id) as purchase_count
    FROM order_items oi
    JOIN cars c ON oi.product_id = c.id
    GROUP BY oi.product_id
    ORDER BY purchase_count DESC
    LIMIT 1
");
$top_car = $result_top_car->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .stat-card { background-color: #2c2c2e; border-radius: 8px; padding: 20px; color: white; }
    </style>
</head>
<body class="bg-dark text-light">
    <div class="container mt-5">
        <h1 class="mb-4">Tổng quan</h1>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-currency-dollar fs-1"></i>
                    <h3 class="mt-2">$<?php echo number_format($total_revenue, 2); ?></h3>
                    <p>Tổng Doanh Thu</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-box-seam fs-1"></i>
                    <h3 class="mt-2"><?php echo $total_orders; ?></h3>
                    <p>Tổng Đơn Hàng</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-people fs-1"></i>
                    <h3 class="mt-2"><?php echo $total_users; ?></h3>
                    <p>Tổng Khách Hàng</p>
                </div>
            </div>
             <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-trophy fs-1"></i>
                    <h3 class="mt-2"><?php echo $top_car ? htmlspecialchars($top_car['brand'] . ' ' . $top_car['model']) : 'N/A'; ?></h3>
                    <p>Xe Bán Chạy Nhất</p>
                </div>
            </div>
        </div>
        <li class="nav-item">
    <a class="nav-link" href="manage_vouchers.php">Quản lý Vouchers</a>
</li>
<div class="mt-5">
    <h2>Doanh thu 7 ngày qua</h2>
    <canvas id="revenueChart"></canvas>
</div>
<div class="mt-5">
    <h2>Đơn hàng gần đây</h2>
    <table class="table table-dark table-hover">
        <thead>
            <tr>
                <th>ID Đơn hàng</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày đặt</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_orders as $order): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                    <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart');
    new Chart(ctx, {
        type: 'bar', // Hoặc 'line' để có biểu đồ đường
        data: {
            labels: <?php echo $chart_labels; ?>, // Dữ liệu ngày tháng từ PHP
            datasets: [{
                label: 'Doanh thu',
                data: <?php echo $chart_data; ?>, // Dữ liệu doanh thu từ PHP
                backgroundColor: 'rgba(255, 193, 7, 0.5)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

        <div class="mt-5">
            <a href="index.php" class="btn btn-primary">Quản Lý Xe</a>
            <a href="orders.php" class="btn btn-info">Quản Lý Đơn Hàng</a>
        </div>
    </div>
</body>
</html>
