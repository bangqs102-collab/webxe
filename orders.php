<?php
// admin/orders.php
require_once '../includes/db_connect.php';
// Thêm file kiểm tra quyền admin của bạn
// require_once 'admin_check.php'; 

$sql = "SELECT o.*, u.username 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);

include 'admin_header.php'; // Sử dụng header admin của bạn
?>
<div class="container mt-4">
    <h3>Quản Lý Đơn Hàng</h3>
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>ID Đơn Hàng</th>
                <th>Tên Khách Hàng</th>
                <th>Tổng Tiền</th>
                <th>Ngày Đặt</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($order = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['username']) ?></td>
                    <td>$<?= number_format($order['total_price'], 2) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                    <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($order['status']) ?></span></td>
                    <td>
                        <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">Xem Chi Tiết</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">Chưa có đơn hàng nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include 'admin_footer.php'; ?>