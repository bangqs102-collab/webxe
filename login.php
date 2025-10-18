<?php include 'includes/header.php'; ?>
<form action="php/auth_handler.php" method="POST">
    </form>

<div class="auth-container">  <div class="auth-form">       <h2 class="auth-title">Đăng Nhập</h2>

        <?php
            if(isset($_GET['error'])){
                if($_GET['error'] == 'wrongpwd' || $_GET['error'] == 'nouser'){
                    echo '<p class="text-danger text-center small">Email hoặc mật khẩu không chính xác.</p>';
                }
            }
            if(isset($_GET['success']) && $_GET['success'] == 'registered'){
                echo '<p class="text-success text-center small">Đăng ký thành công! Vui lòng đăng nhập.</p>';
            }
        ?>

        <form action="php/auth_handler.php" method="POST" class="mt-4">
            <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Địa chỉ Email" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Mật khẩu" required>
            </div>
            <div class="d-grid mt-4">
                 <button type="submit" name="login" class="btn btn-submit">Đăng Nhập</button>
            </div>
        </form>
        <p class="auth-link">Chưa có tài khoản? <a href="register.php">Đăng ký tại đây</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>