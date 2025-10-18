<?php
// Nhúng file kết nối CSDL
require 'includes/db_connect.php';
// Sửa 'products' thành 'cars' hoặc tên bảng đúng của bạn
$sql = "SELECT id, name, image_url FROM cars WHERE name LIKE ? LIMIT 5";
// Kiểm tra xem có từ khóa tìm kiếm được gửi lên không
if (isset($_GET['query'])) {
    $searchTerm = trim($_GET['query']);

    // Chỉ thực hiện tìm kiếm nếu từ khóa không rỗng
    if (!empty($searchTerm)) {
        // Chuẩn bị truy vấn an toàn bằng prepared statement để chống SQL Injection
        // Dấu '%' cho phép tìm kiếm các xe có tên BẮT ĐẦU bằng từ khóa
        $sql = "SELECT id, name, image_url FROM products WHERE name LIKE ? LIMIT 5";
        $stmt = $conn->prepare($sql);
        
        $likeTerm = $searchTerm . '%';
        $stmt->bind_param("s", $likeTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Lặp qua các kết quả và tạo HTML
            while ($row = $result->fetch_assoc()) {
                echo '<a href="product_detail.php?id=' . htmlspecialchars($row['id']) . '" class="search-result-item">';
                // Giả sử bạn có cột image_url cho ảnh xe
                // echo '<img src="' . htmlspecialchars($row['image_url']) . '" alt="">'; 
                echo '<span>' . htmlspecialchars($row['name']) . '</span>';
                echo '</a>';
            }
        } else {
            echo '<div class="search-result-item"><span>Không tìm thấy kết quả</span></div>';
        }
    }
}
?>