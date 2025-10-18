<?php include "includes/header.php"; ?>

<div class="auth-container"> <div class="auth-form">      <h2 class="auth-title">Đăng Ký Tài Khoản</h2>

        <?php
            // Hiển thị các thông báo lỗi có thể được truyền qua URL
            if(isset($_GET['error'])) {
                if($_GET['error'] == 'emptyinput') {
                    echo '<p class="text-danger text-center small">Vui lòng điền đầy đủ thông tin.</p>';
                } else if($_GET['error'] == 'invalidusername') {
                    echo '<p class="text-danger text-center small">Tên người dùng không hợp lệ.</p>';
                } else if($_GET['error'] == 'invalidemail') {
                    echo '<p class="text-danger text-center small">Địa chỉ email không hợp lệ.</p>';
                } else if($_GET['error'] == 'passwordmismatch') {
                    echo '<p class="text-danger text-center small">Mật khẩu xác nhận không khớp.</p>';
                } else if($_GET['error'] == 'usernametaken') {
                    echo '<p class="text-danger text-center small">Tên người dùng hoặc email đã tồn tại.</p>';
                } else if($_GET['error'] == 'stmtfailed') {
                    echo '<p class="text-danger text-center small">Đã có lỗi xảy ra, vui lòng thử lại!</p>';
                }
            }
        ?>

        <form action="php/auth_handler.php" method="POST" class="mt-4">
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Tên người dùng" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Địa chỉ Email" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Mật khẩu" required>
            </div>
             <div class="mb-3">
                <input type="password" class="form-control" name="confirm_password" placeholder="Xác nhận Mật khẩu" required>
            </div>
            <div class="d-grid mt-4">
                <button type="submit" name="register" class="btn btn-submit">Đăng Ký</button>
            </div>
        </form>

        <div class="auth-link">
            Đã có tài khoản? <a href="login.php">Đăng nhập tại đây</a>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>