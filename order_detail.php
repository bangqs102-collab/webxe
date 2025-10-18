<?php
// admin/order_detail.php
require_once '../includes/db_connect.php';
// require_once 'admin_check.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($order_id === 0) {
    die("ID đơn hàng không hợp lệ.");
}

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $update_stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $order_id);
    $update_stmt->execute();
    $update_stmt->close();
}

// Lấy thông tin chi tiết đơn hàng
$sql = "SELECT oi.*, p.brand, p.model, p.image_url FROM order_items oi JOIN cars p ON oi.product_id = p.id WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
$stmt->close();

include 'admin_header.php';
?>
<div class="container mt-4">
    <h3>Chi Tiết Đơn Hàng #<?= $order_id ?></h3>
    
    <form method="POST" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <select name="status" class="form-select bg-dark text-white">
                    <option value="Pending">Chưa giải quyết</option>
                    <option value="Processing">Xử lý</option>
                    <option value="Completed">Hoàn thành</option>
                    <option value="Cancelled">Hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" name="update_status" class="btn btn-primary">Cập nhật Trạng Thái</button>
            </div>
        </div>
    </form>

    <table class="table table-dark">
        <thead>
            <tr>
                <th colspan="2">Sản phẩm</th>
                <th>Giá lúc mua</th>
                <th>Số lượng</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = $items_result->fetch_assoc()): ?>
            <tr>
                <td><img src="../<?= htmlspecialchars($item['image_url']) ?>" width="100"></td>
                <td><?= htmlspecialchars($item['brand'] . ' ' . $item['model']) ?></td>
                <td>$<?= number_format($item['price_at_purchase'], 2) ?></td>
                <td><?= $item['quantity'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include 'admin_footer.php'; ?>