<?php
// my_orders.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'includes/db_connect.php';

// Bảo mật: Yêu cầu người dùng phải đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy thông tin người dùng từ session
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// --- PHÂN QUYỀN: KIỂM TRA VAI TRÒ NGƯỜI DÙNG ---

if ($user_role === 'admin') {
    // NẾU LÀ ADMIN: Lấy tất cả đơn hàng và thông tin người đặt
    $sql = "SELECT o.id, o.order_date, o.total_price, o.status, u.username
            FROM orders o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.order_date DESC";
    $stmt = $conn->prepare($sql);
} else {
    // NẾU LÀ USER: Chỉ lấy đơn hàng của chính họ
    $sql = "SELECT id, order_date, total_price, status FROM orders WHERE user_id = ? ORDER BY order_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include 'includes/header.php';
?>

<div class="container my-5" style="color: white;">
    <div class="text-center mb-5">
        <h1 class="display-5">
            <?php echo ($user_role === 'admin') ? 'Quản Lý Tất Cả Đơn Hàng' : 'Lịch Sử Đơn Hàng Của Bạn'; ?>
        </h1>
        <p class="lead text-muted">Đây là danh sách các đơn hàng đã được đặt trên hệ thống.</p>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> text-center">
            <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle">
            <thead>
                <tr class="text-center">
                    <th>Mã Đơn</th>
                    <?php if ($user_role === 'admin'): ?>
                        <th>Người Đặt</th>
                    <?php endif; ?>
                    <th>Ngày Đặt</th>
                    <th>Tổng Tiền</th>
                    <th>Trạng Thái</th>
                    <?php if ($user_role !== 'admin'): ?>
                        <th>Hành Động</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="<?php echo ($user_role === 'admin') ? '6' : '5'; ?>" class="text-center py-4">Chưa có đơn hàng nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr class="text-center">
                            <td>#<?= $order['id'] ?></td>
                             <?php if ($user_role === 'admin'): ?>
                                <td><?= htmlspecialchars($order['username']) ?></td>
                            <?php endif; ?>
                            <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                            <td>$<?= number_format($order['total_price']) ?></td>
                            <td>
                                <?php
                                    $status = $order['status'];
                                    $badge_class = 'bg-secondary'; // Mặc định
                                    if ($status == 'Chờ xác nhận') $badge_class = 'bg-warning text-dark';
                                    if ($status == 'Processing' || $status == 'Đang xử lý') $badge_class = 'bg-info text-dark';
                                    if ($status == 'Completed' || $status == 'Đã hoàn thành') $badge_class = 'bg-success';
                                    if ($status == 'Đã hủy') $badge_class = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($status) ?></span>
                            </td>
                            <?php if ($user_role !== 'admin'): ?>
                                <td>
                                    <?php if ($order['status'] == 'Chờ xác nhận'): ?>
                                        <a href="php/cancel_order.php?order_id=<?= $order['id'] ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                                           Hủy Đơn Hàng
                                        </a>
                                    <?php else: ?>
                                        ---
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include 'includes/footer.php';
$conn->close();
?>