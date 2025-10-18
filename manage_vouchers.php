<?php
// admin/manage_vouchers.php
require 'admin_check.php'; // Đảm bảo chỉ admin có thể truy cập
require '../includes/db_connect.php'; // Kết nối CSDL

// Lấy tất cả voucher từ CSDL
$sql = "SELECT * FROM vouchers ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$vouchers = mysqli_fetch_all($result, MYSQLI_ASSOC);

include 'admin_header.php'; // Giao diện đầu trang admin
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Quản lý Vouchers</h2>
        <a href="add_voucher.php" class="btn btn-primary">Thêm Voucher mới</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Mã Code</th>
                <th>Loại</th>
                <th>Giá trị</th>
                <th>Ngày hết hạn</th>
                <th>Lượt sử dụng</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($vouchers)): ?>
                <tr>
                    <td colspan="8" class="text-center">Chưa có voucher nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($vouchers as $voucher): ?>
                    <tr>
                        <td><?= $voucher['id'] ?></td>
                        <td><strong><?= htmlspecialchars($voucher['code']) ?></strong></td>
                        <td><?= $voucher['discount_type'] === 'percentage' ? 'Phần trăm' : 'Số tiền cố định' ?></td>
                        <td>
                            <?php 
                                if ($voucher['discount_type'] === 'percentage') {
                                    echo $voucher['discount_value'] . '%';
                                } else {
                                    echo '$' . number_format($voucher['discount_value']);
                                }
                            ?>
                        </td>
                        <td><?= $voucher['expiry_date'] ? date('d/m/Y', strtotime($voucher['expiry_date'])) : 'Không hết hạn' ?></td>
                        <td><?= $voucher['times_used'] ?> / <?= $voucher['usage_limit'] ?></td>
                        <td>
                            <span class="badge <?= $voucher['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $voucher['is_active'] ? 'Hoạt động' : 'Vô hiệu hóa' ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit_voucher.php?id=<?= $voucher['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                            <a href="delete_voucher.php?id=<?= $voucher['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa voucher này?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'admin_footer.php'; ?>