<?php
// admin/edit_voucher.php
require 'admin_check.php';
require '../includes/db_connect.php';

$voucher_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $code = strtoupper(trim($_POST['code']));
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : NULL;
    $usage_limit = $_POST['usage_limit'];
    $is_active = $_POST['is_active'];

    $sql = "UPDATE vouchers SET code = ?, discount_type = ?, discount_value = ?, expiry_date = ?, usage_limit = ?, is_active = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdsiii", $code, $discount_type, $discount_value, $expiry_date, $usage_limit, $is_active, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: manage_vouchers.php");
        exit();
    } else {
        $error = "Lỗi: Không thể cập nhật voucher.";
    }
}

// Lấy thông tin voucher hiện tại để hiển thị
$sql_select = "SELECT * FROM vouchers WHERE id = ?";
$stmt_select = mysqli_prepare($conn, $sql_select);
mysqli_stmt_bind_param($stmt_select, "i", $voucher_id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$voucher = mysqli_fetch_assoc($result);

if (!$voucher) {
    die("Không tìm thấy voucher.");
}

include 'admin_header.php';
?>

<div class="container mt-4">
    <h2>Sửa Voucher: <?= htmlspecialchars($voucher['code']) ?></h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="edit_voucher.php?id=<?= $voucher_id ?>" method="POST">
        <input type="hidden" name="id" value="<?= $voucher['id'] ?>">
        
        <div class="mb-3">
            <label for="code" class="form-label">Mã Voucher</label>
            <input type="text" class="form-control" id="code" name="code" value="<?= htmlspecialchars($voucher['code']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="discount_type" class="form-label">Loại giảm giá</label>
            <select class="form-select" id="discount_type" name="discount_type">
                <option value="percentage" <?= $voucher['discount_type'] == 'percentage' ? 'selected' : '' ?>>Phần trăm (%)</option>
                <option value="fixed" <?= $voucher['discount_type'] == 'fixed' ? 'selected' : '' ?>>Số tiền cố định ($)</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="discount_value" class="form-label">Giá trị giảm</label>
            <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value" value="<?= $voucher['discount_value'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="usage_limit" class="form-label">Tổng lượt sử dụng</label>
            <input type="number" class="form-control" id="usage_limit" name="usage_limit" value="<?= $voucher['usage_limit'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="expiry_date" class="form-label">Ngày hết hạn</label>
            <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?= $voucher['expiry_date'] ?>">
        </div>
         <div class="mb-3">
            <label for="is_active" class="form-label">Trạng thái</label>
            <select class="form-select" id="is_active" name="is_active">
                <option value="1" <?= $voucher['is_active'] == 1 ? 'selected' : '' ?>>Hoạt động</option>
                <option value="0" <?= $voucher['is_active'] == 0 ? 'selected' : '' ?>>Vô hiệu hóa</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="manage_vouchers.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php include 'admin_footer.php'; ?>