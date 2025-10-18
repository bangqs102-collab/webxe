<?php
// Luôn bắt đầu session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kết nối database và header
require_once 'includes/db_connect.php';
include 'includes/header.php';

// Truy vấn để lấy các voucher hợp lệ
// Điều kiện: is_active = 1, chưa hết hạn, và chưa hết lượt sử dụng
$sql = "SELECT code, discount_type, discount_value, expiry_date, usage_limit, times_used
        FROM vouchers
        WHERE is_active = 1
        AND (expiry_date IS NULL OR expiry_date >= CURDATE())
        AND (usage_limit IS NULL OR times_used < usage_limit)";
$result = $conn->query($sql);
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4">Mã Khuyến Mãi & Voucher</h1>
        <p class="lead text-muted">Sử dụng mã dưới đây để nhận được ưu đãi hấp dẫn khi thanh toán!</p>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show text-center" role="alert">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>

    <div class="row">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($voucher = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-dark border-secondary h-100 shadow-sm voucher-card">
                        <div class="card-body d-flex flex-column text-center p-4">
                            <h5 class="card-title text-warning mb-3">
                                <?php
                                if ($voucher['discount_type'] == 'percentage') {
                                    echo 'GIẢM ' . rtrim(rtrim($voucher['discount_value'], '0'), '.') . '%';
                                } else {
                                    echo 'GIẢM ' . number_format($voucher['discount_value'], 0) . ' VNĐ';
                                }
                                ?>
                            </h5>
                            <p class="card-text small">Cho tất cả các đơn hàng.</p>
                            
                            <div class="my-3">
                                <span class="voucher-code"><?php echo htmlspecialchars($voucher['code']); ?></span>
                            </div>

                            <div class="mt-auto">
                                <button class="btn btn-outline-warning btn-copy-code" 
                                        data-code="<?php echo htmlspecialchars($voucher['code']); ?>">Sao chép mã
                                </button>
                            </div>

                            <hr class="border-secondary my-3">

                            <div class="voucher-details text-muted small">
                                <?php if ($voucher['expiry_date']): ?>
                                    <span>HSD: <?php echo date("d/m/Y", strtotime($voucher['expiry_date'])); ?></span>
                                <?php else: ?>
                                    <span>Không thời hạn</span>
                                <?php endif; ?>
                                
                                <?php if ($voucher['usage_limit']):
                                    $remaining = $voucher['usage_limit'] - $voucher['times_used'];
                                ?>
                                    <span class="ms-2">| Còn: <?php echo $remaining; ?> lượt</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center text-muted">Hiện tại không có mã khuyến mãi nào, vui lòng quay lại sau.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tìm tất cả các nút có class là 'btn-copy-code'
    const copyButtons = document.querySelectorAll('.btn-copy-code');

    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Lấy mã từ thuộc tính data-code
            const codeToCopy = this.dataset.code;

            // Sử dụng Clipboard API để sao chép
            navigator.clipboard.writeText(codeToCopy).then(() => {
                // Khi sao chép thành công
                const originalText = this.textContent;
                this.textContent = 'Đã sao chép!';
                this.classList.add('btn-success'); // Đổi màu nút để thông báo
                this.classList.remove('btn-outline-warning');

                // Sau 2 giây, trả lại trạng thái ban đầu
                setTimeout(() => {
                    this.textContent = originalText;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-warning');
                }, 2000);

            }).catch(err => {
                console.error('Lỗi khi sao chép mã: ', err);
                alert('Sao chép thất bại!');
            });
        });
    });
});
</script>

<?php
// Bao gồm footer
include 'includes/footer.php';
$conn->close();
?>