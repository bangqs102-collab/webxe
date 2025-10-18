<?php
session_start();
require_once 'admin_check.php'; // Đảm bảo chỉ admin mới thực thi được file này
require_once '../includes/db_connect.php';

// Xác định hành động từ cả GET (cho việc xóa) và POST (cho thêm/sửa)
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'add_car':
    case 'update_car':
        // Các hành động này phải được gửi bằng POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['message'] = 'Lỗi: Phương thức truy cập không hợp lệ.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit();
        }

        // Lấy dữ liệu từ form
        $brand = $_POST['brand'] ?? '';
        $model = $_POST['model'] ?? '';
        $price = $_POST['price'] ?? 0;
        $description = $_POST['description'] ?? '';
        $year = $_POST['year'] ?? 0;
        $mileage = $_POST['mileage'] ?? 0;
        $quantity = $_POST['quantity'] ?? 0;
        $current_image_url = $_POST['current_image_url'] ?? '';
        $image_url = $current_image_url;

        // Xử lý upload ảnh mới
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/";
            $image_name = uniqid() . '-' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = "uploads/" . $image_name;
            }
        }

        if ($action == 'add_car') {
            $sql = "INSERT INTO cars (brand, model, price, description, year, mileage, image_url, quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdssisi", $brand, $model, $price, $description, $year, $mileage, $image_url, $quantity);
            $_SESSION['message'] = 'Thêm xe mới thành công!';
        } else { // update_car
            $car_id = (int)$_POST['id'];
            $sql = "UPDATE cars SET brand=?, model=?, price=?, description=?, year=?, mileage=?, image_url=?, quantity=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdssisii", $brand, $model, $price, $description, $year, $mileage, $image_url, $quantity, $car_id);
            $_SESSION['message'] = 'Cập nhật thông tin xe thành công!';
        }

        if ($stmt->execute()) {
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Lỗi cơ sở dữ liệu: ' . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        break;

    case 'delete_car':
        // Hành động xóa phải có ID
        if (!isset($_GET['id'])) {
            $_SESSION['message'] = 'Lỗi: Không tìm thấy ID xe để xóa.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit();
        }
        $car_id = (int)$_GET['id'];
        
        $sql = "DELETE FROM cars WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $car_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Xóa xe thành công!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Lỗi khi xóa xe: ' . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        break;

    default:
        $_SESSION['message'] = 'Lỗi: Hành động không hợp lệ.';
        $_SESSION['message_type'] = 'danger';
        break;
}

// Sau khi xử lý xong, chuyển hướng về trang danh sách xe
header('Location: index.php');
exit();
?>

