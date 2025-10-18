<?php
require_once 'admin_check.php';
require_once '../includes/db_connect.php';

// === PHẦN 1: LẤY TẤT CẢ DỮ LIỆU THỐNG KÊ ===

// Thống kê Doanh thu (tổng)
$result_revenue = $conn->query("SELECT SUM(total_price) as total_revenue FROM orders WHERE status = 'Completed'");
$total_revenue = $result_revenue->fetch_assoc()['total_revenue'] ?? 0;

// Thống kê Tổng số đơn hàng
$result_orders = $conn->query("SELECT COUNT(id) as total_orders FROM orders");
$total_orders = $result_orders->fetch_assoc()['total_orders'] ?? 0;

// Thống kê Tổng số khách hàng
$result_users = $conn->query("SELECT COUNT(id) as total_users FROM users WHERE role != 'admin'");
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

// Dữ liệu cho biểu đồ doanh thu 7 ngày
$revenue_by_day = [];
$sql_chart = "SELECT DATE(order_date) as sale_date, SUM(total_price) as daily_revenue
              FROM orders
              WHERE status = 'Completed' AND order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
              GROUP BY DATE(order_date)
              ORDER BY sale_date ASC";
$result_chart = $conn->query($sql_chart);
if($result_chart) {
    while ($row = $result_chart->fetch_assoc()) {
        $revenue_by_day[] = $row;
    }
}
$chart_labels = json_encode(array_column($revenue_by_day, 'sale_date'));
$chart_data = json_encode(array_column($revenue_by_day, 'daily_revenue'));

// Dữ liệu 5 đơn hàng mới nhất
$sql_recent_orders = "SELECT o.id, u.username, o.total_price, o.status, o.order_date
                      FROM orders o
                      JOIN users u ON o.user_id = u.id
                      ORDER BY o.order_date DESC
                      LIMIT 5";
$result_recent_orders = $conn->query($sql_recent_orders);
$recent_orders = $result_recent_orders->fetch_all(MYSQLI_ASSOC);

// Dữ liệu cho bảng quản lý xe, sắp xếp theo ID giảm dần để xe mới nhất lên đầu
$sql_cars = "SELECT * FROM cars ORDER BY id DESC";
$result_cars = $conn->query($sql_cars);

include 'admin_header.php';
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- ĐÃ XÓA DÒNG H1 Ở ĐÂY ĐỂ TRÁNH BỊ LẶP LẠI -->
        <div>
            <a href="manage_car.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Thêm Xe Mới</a>
            <a href="manage_vouchers.php" class="btn btn-primary">Quản Lý Vouchers</a>
            <a href="orders.php" class="btn btn-warning">Quản Lý Đơn Hàng</a>
            <a href="../index.php" class="btn btn-secondary">Về trang chủ</a>
        </div>
    </div>

    <!-- Hiển thị thông báo (nếu có) -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>

    <h2 class="mb-3">Tổng quan</h2>
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card text-center">
                <i class="bi bi-currency-dollar fs-1"></i>
                <h3 class="mt-2">$<?php echo number_format($total_revenue, 2); ?></h3>
                <p>Tổng Doanh Thu</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card text-center">
                <i class="bi bi-box-seam fs-1"></i>
                <h3 class="mt-2"><?php echo $total_orders; ?></h3>
                <p>Tổng Đơn Hàng</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card text-center">
                <i class="bi bi-people fs-1"></i>
                <h3 class="mt-2"><?php echo $total_users; ?></h3>
                <p>Tổng Khách Hàng</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card text-center">
                <i class="bi bi-trophy fs-1"></i>
                <h3 class="mt-2"><?php echo $top_car ? htmlspecialchars($top_car['brand'] . ' ' . $top_car['model']) : 'N/A'; ?></h3>
                <p>Xe Bán Chạy Nhất</p>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-lg-7">
            <h2 class="mb-3">Doanh thu 7 ngày qua</h2>
            <canvas id="revenueChart"></canvas>
        </div>
        <div class="col-lg-5">
            <h2 class="mb-3">Đơn hàng gần đây</h2>
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td>$<?php echo number_format($order['total_price']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h2 class="mb-3">Quản Lý Xe</h2>
    <table class="table table-dark table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình ảnh</th>
                <th>Hãng Xe</th>
                <th>Dòng Xe</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_cars->num_rows > 0) {
                while ($row = $result_cars->fetch_assoc()) {
            ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><img src='../<?= htmlspecialchars($row['image_url']) ?>' width='100' style='object-fit: cover; border-radius: 4px;'></td>
                    <td><?= htmlspecialchars($row['brand']) ?></td>
                    <td><?= htmlspecialchars($row['model']) ?></td>
                    <td>$<?= number_format($row['price']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td>
                        <a href='manage_car.php?id=<?= $row['id'] ?>' class='btn btn-primary btn-sm'><i class='bi bi-pencil-square'></i> Sửa</a>
                        <!-- SỬA: Sửa lại link xóa cho đúng với car_handler.php -->
                        <a href='car_handler.php?action=delete_car&id=<?= $row['id'] ?>' class='btn btn-danger btn-sm' onclick='return confirm("Bạn có chắc chắn muốn xóa xe này?")'><i class='bi bi-trash'></i> Xóa</a>
                    </td>
                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>Chưa có xe nào.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $chart_labels; ?>,
            datasets: [{
                label: 'Doanh thu',
                data: <?php echo $chart_data; ?>,
                backgroundColor: 'rgba(255, 193, 7, 0.5)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#fff' }
                },
                x: {
                    ticks: { color: '#fff' }
                }
            },
            plugins: {
                legend: {
                    labels: { color: '#fff' }
                }
            }
        }
    });
</script>
<?php include 'admin_footer.php'; ?>

