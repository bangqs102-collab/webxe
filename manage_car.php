<?php
require_once 'admin_check.php';
require_once '../includes/db_connect.php';

$edit_mode = false;
$car = null;
$page_title = "Thêm Xe Mới";
// Khởi tạo tất cả các biến
$brand = $model = $price = $description = $year = $mileage = $image_url = $quantity = '';

// --- KIỂM TRA NẾU ĐANG Ở CHẾ ĐỘ SỬA ---
if (isset($_GET['id'])) {
    $edit_mode = true;
    $page_title = "Sửa Thông Tin Xe";
    $car_id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    if ($car) {
        // Gán dữ liệu vào biến để hiển thị trên form
        extract($car);
    }
}

// --- XỬ LÝ KHI FORM ĐƯỢC GỬI ĐI ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $year = $_POST['year'];
    $mileage = $_POST['mileage'];
    $quantity = $_POST['quantity']; // SỬA 1: Lấy giá trị số lượng từ form
    $current_image_url = $_POST['current_image_url'] ?? '';

    // Xử lý upload ảnh mới
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        // Tạo tên file duy nhất để tránh bị ghi đè
        $image_name = uniqid() . '-' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_url = "uploads/" . $image_name;
    } else {
        $image_url = $current_image_url;
    }

    if ($edit_mode) {
        // --- CẬP NHẬT XE ---
        $car_id = (int)$_POST['id'];
        // SỬA 2: Thêm `quantity` vào câu lệnh UPDATE
        $sql = "UPDATE cars SET brand=?, model=?, price=?, description=?, year=?, mileage=?, image_url=?, quantity=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        // Cập nhật bind_param, thêm 'i' cho quantity
        $stmt->bind_param("ssdssisii", $brand, $model, $price, $description, $year, $mileage, $image_url, $quantity, $car_id);
    } else {
        // --- THÊM XE MỚI ---
        // SỬA 3: Thêm `quantity` vào câu lệnh INSERT
        $sql = "INSERT INTO cars (brand, model, price, description, year, mileage, image_url, quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        // Cập nhật bind_param, thêm 'i' cho quantity
        $stmt->bind_param("ssdssisi", $brand, $model, $price, $description, $year, $mileage, $image_url, $quantity);
    }

    if ($stmt->execute()) {
        header("Location: index.php"); // Chuyển hướng về trang danh sách xe
        exit();
    } else {
        $error_message = "Có lỗi xảy ra, vui lòng thử lại.";
    }
}

include 'admin_header.php';
?>

<div class="container my-5">
    <h1 class="mb-4"><?= $page_title ?></h1>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <?php if ($edit_mode): ?>
            <input type="hidden" name="id" value="<?= $car['id'] ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="brand" class="form-label">Hãng Xe</label>
            <input type="text" class="form-control" id="brand" name="brand" value="<?= htmlspecialchars($brand) ?>" required>
        </div>
        <div class="mb-3">
            <label for="model" class="form-label">Dòng Xe</label>
            <input type="text" class="form-control" id="model" name="model" value="<?= htmlspecialchars($model) ?>" required>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Số lượng</label>
            <!-- SỬA 4: Thêm `value` để hiển thị số lượng hiện tại khi sửa -->
            <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($quantity) ?>" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Giá</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($price) ?>" required>
        </div>
        <div class="mb-3">
            <label for="year" class="form-label">Năm sản xuất</label>
            <input type="number" class="form-control" id="year" name="year" value="<?= htmlspecialchars($year) ?>" required>
        </div>
        <div class="mb-3">
            <label for="mileage" class="form-label">Số dặm đã đi</label>
            <input type="number" class="form-control" id="mileage" name="mileage" value="<?= htmlspecialchars($mileage) ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($description) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Hình ảnh</label>
            <input type="file" class="form-control" id="image" name="image">
            <?php if ($edit_mode && !empty($image_url)): ?>
                <p class="mt-2">Ảnh hiện tại:</p>
                <img src="../<?= htmlspecialchars($image_url) ?>" width="150">
                <input type="hidden" name="current_image_url" value="<?= htmlspecialchars($image_url) ?>">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary"><?= $edit_mode ? 'Cập nhật' : 'Thêm Xe' ?></button>
        <a href="index.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php include 'admin_footer.php'; ?>
