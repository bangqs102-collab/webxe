<?php
session_start();
require_once '../includes/db_connect.php';

// --- BẢO MẬT: KIỂM TRA XEM ADMIN ĐÃ ĐĂNG NHẬP CHƯA ---
// Thay 'admin_logged_in' bằng biến session bạn dùng để kiểm tra đăng nhập
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Nếu chưa đăng nhập, chuyển hướng về trang login
    header("Location: login.php");
    exit();
}


// 1. Kiểm tra xem ID có được gửi lên và có phải là một số hợp lệ không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $car_id = (int)$_GET['id'];

    // --- (Tùy chọn nhưng nên có) Xóa file hình ảnh liên quan đến xe ---
    // Trước khi xóa record trong DB, lấy đường dẫn ảnh để xóa file
    $sql_select_image = "SELECT image_url FROM cars WHERE id = ?";
    if ($stmt_select = mysqli_prepare($conn, $sql_select_image)) {
        mysqli_stmt_bind_param($stmt_select, "i", $car_id);
        mysqli_stmt_execute($stmt_select);
        $result = mysqli_stmt_get_result($stmt_select);
        if ($row = mysqli_fetch_assoc($result)) {
            $image_path = '../' . $row['image_url']; // Đường dẫn vật lý đến file ảnh
            if (file_exists($image_path)) {
                unlink($image_path); // Hàm unlink() dùng để xóa file
            }
        }
        mysqli_stmt_close($stmt_select);
    }


    // 2. Chuẩn bị câu lệnh DELETE bằng Prepared Statement để chống SQL Injection
    $sql = "DELETE FROM cars WHERE id = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Gán biến car_id vào câu lệnh
        mysqli_stmt_bind_param($stmt, "i", $car_id);
        
        // 3. Thực thi câu lệnh
        if (mysqli_stmt_execute($stmt)) {
            // Nếu xóa thành công, tạo một thông báo và chuyển hướng
            $_SESSION['message'] = "Đã xóa xe thành công!";
        } else {
            // Nếu có lỗi, tạo thông báo lỗi
            $_SESSION['message'] = "Lỗi: Không thể xóa xe. " . mysqli_error($conn);
        }
        
        // Đóng statement
        mysqli_stmt_close($stmt);
    }
} else {
    // Nếu ID không hợp lệ
    $_SESSION['message'] = "Lỗi: ID xe không hợp lệ.";
}

// 4. Đóng kết nối CSDL
mysqli_close($conn);

// 5. Chuyển hướng người dùng trở lại trang danh sách xe
header("Location: index.php");
exit();

?>