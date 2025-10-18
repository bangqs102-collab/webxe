<?php
// admin/add_voucher.php
require 'admin_check.php';
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = strtoupper(trim($_POST['code']));
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : NULL;
    $usage_limit = $_POST['usage_limit'];
    $is_active = $_POST['is_active'];

    $sql = "INSERT INTO vouchers (code, discount_type, discount_value, expiry_date, usage_limit, is_active) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdsii", $code, $discount_type, $discount_value, $expiry_date, $usage_limit, $is_active);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: manage_vouchers.php");
        exit();
    } else {
        $error = "Lỗi: Không thể thêm voucher.";
    }
}

include 'admin_header.php';
?>

<div class="container mt-4">
    <h2>Thêm Voucher mới</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="add_voucher.php" method="POST">
        <div class="mb-3">
            <label for="code" class="form-label">Mã Voucher (VD: SALE50)</label>
            <input type="text" class="form-control" id="code" name="code" required>
        </div>
        <div class="mb-3">
            <label for="discount_type" class="form-label">Loại giảm giá</label>
            <select class="form-select" id="discount_type" name="discount_type">
                <option value="percentage">Phần trăm (%)</option>
                <option value="fixed">Số tiền cố định ($)</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="discount_value" class="form-label">Giá trị giảm</label>
            <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value" required>
        </div>
        <div class="mb-3">
            <label for="usage_limit" class="form-label">Tổng lượt sử dụng</label>
            <input type="number" class="form-control" id="usage_limit" name="usage_limit" value="100" required>
        </div>
        <div class="mb-3">
            <label for="expiry_date" class="form-label">Ngày hết hạn (để trống nếu không có)</label>
            <input type="date" class="form-control" id="expiry_date" name="expiry_date">
        </div>
         <div class="mb-3">
            <label for="is_active" class="form-label">Trạng thái</label>
            <select class="form-select" id="is_active" name="is_active">
                <option value="1">Hoạt động</option>
                <option value="0">Vô hiệu hóa</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Thêm Voucher</button>
        <a href="manage_vouchers.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php include 'admin_footer.php'; ?>