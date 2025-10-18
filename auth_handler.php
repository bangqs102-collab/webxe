<?php
session_start();
require_once '../includes/db_connect.php';

// XỬ LÝ ĐĂNG KÝ
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // --- Validation cơ bản ---
    if (empty($username) || empty($email) || empty($password)) {
        header("Location: ../register.php?error=emptyfields");
        exit();
    }
    
    // --- Kiểm tra email có tồn tại không ---
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        header("Location: ../register.php?error=emailtaken");
        exit();
    }

    // --- Băm mật khẩu (An toàn) ---
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // --- Thêm người dùng mới vào DB ---
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        header("Location: ../login.php?success=registered");
        exit();
    } else {
        header("Location: ../register.php?error=dberror");
        exit();
    }
}

// XỬ LÝ ĐĂNG NHẬP
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: ../login.php?error=emptyfields");
        exit();
    }
}

    // Trong file php/auth_handler.php, bên trong khối if (isset($_POST['login']))

// Thay thế dòng này:
$sql = "SELECT id, username, password FROM users WHERE email = ?";

// Bằng dòng này (thêm 'role' vào):
$sql = "SELECT id, username, password, role FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $passwordCheck = password_verify($password, $row['password']);
    if ($passwordCheck == true) {
        // Cập nhật phần này để lưu thêm 'role'
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role']; // <-- DÒNG MỚI QUAN TRỌNG

        header("Location: ../index.php");
        exit();
    }
 } else {
        // ... phần còn lại giữ nguyên
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // --- So sánh mật khẩu đã nhập với mật khẩu đã băm trong DB ---
        $passwordCheck = password_verify($password, $row['password']);
        if ($passwordCheck == true) {
            // Đăng nhập thành công, bắt đầu session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: ../index.php"); // Chuyển hướng đến trang chủ
            exit();
        } else {
            header("Location: ../login.php?error=wrongpwd");
            exit();
        }
    } else {
        header("Location: ../login.php?error=nouser");
        exit();
    }
}
?>