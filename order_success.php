<?php
// MỚI: Toàn bộ khối logic PHP được đưa lên ĐẦU TIÊN
// BƯỚC 1: Bắt đầu session và kết nối CSDL
session_start();
require_once 'includes/db_connect.php';

// BƯỚC 2: Kiểm tra quyền truy cập và dữ liệu đầu vào.
// Nếu không hợp lệ, chuyển hướng NGAY LẬP TỨC.
if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header('Location: login.php'); // Chuyển đến trang đăng nhập nếu chưa đăng nhập
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// BƯỚC 3: Truy vấn dữ liệu sau khi đã xác thực
// --- CÂU LỆNH SQL ĐÃ SỬA LẠI THEO ĐÚNG CẤU TRÚC BẢNG CỦA BẠN ---
$sql = "SELECT 
            oi.product_id, 
            c.brand,
            c.model,
            c.image_url as image,
            r.id as review_id
        FROM order_items oi
        JOIN cars c ON oi.product_id = c.id
        LEFT JOIN reviews r ON oi.product_id = r.product_id AND oi.order_id = r.order_id AND r.user_id = ?
        WHERE oi.order_id = ?
        GROUP BY oi.product_id";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Lỗi prepare SQL: " . $conn->error); // Kiểm tra lỗi prepare
}
$stmt->bind_param("ii", $user_id, $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// BƯỚC 4: BÂY GIỜ MỚI BẮT ĐẦU INCLUDE PHẦN GIAO DIỆN (HTML)
include 'includes/header.php';
?>

<style>
    /* CSS cho trang đánh giá */
    .rating { display: inline-block; }
    .rating input { display: none; }
    .rating label {
        float: right;
        cursor: pointer;
        color: #ccc;
        transition: color 0.2s;
        font-size: 2rem;
    }
    .rating label:before { content: '★'; }
    .rating input:checked ~ label,
    .rating label:hover,
    .rating label:hover ~ label { color: #f2b600; }
    .review-box {
        background-color: #222; 
        padding: 20px; 
        border-radius: 8px; 
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    .review-box img { width: 150px; height: auto; margin-right: 20px; border-radius: 4px; object-fit: cover; }
    .review-form-container { flex-grow: 1; }
    .reviewed-message { color: #00e676; font-weight: bold; }
</style>

<div class="container" style="padding-top: 50px; padding-bottom: 50px;">
    <div style="text-align: center; margin-bottom: 40px;">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #00e676;">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 16.17l7.59-7.59L19 10l-9 9z" fill="currentColor"></path>
        </svg>
        <h1 style="color: #fff;">Đặt Hàng Thành Công!</h1>
        <p style="color: #aaa;">Cảm ơn bạn đã mua hàng. Đơn hàng #<?php echo htmlspecialchars($order_id); ?> của bạn đang được xử lý.</p>
        <a href="my_orders.php" class="btn btn-warning">Xem Lịch Sử Đơn Hàng</a>
    </div>

    <hr style="border-color: #444;">

    <div class="product-reviews-section" style="margin-top: 40px;">
        <h2 style="color: #fff; text-align: center; margin-bottom: 30px;">Đánh giá các sản phẩm đã mua</h2>

        <?php if (isset($_GET['review_status']) && $_GET['review_status'] == 'success'): ?>
            <div class="alert alert-success">Cảm ơn bạn đã gửi đánh giá!</div>
        <?php endif; ?>

        <?php foreach ($order_items as $item): ?>
            <div class="review-box">
                <img src="<?php echo htmlspecialchars($item['image'] ?? 'path/to/default-image.png'); ?>" alt="<?php echo htmlspecialchars($item['brand'] . ' ' . $item['model']); ?>">
                <div class="review-form-container">
                    <!-- SỬA: Hiển thị brand và model -->
                    <h5 style="color: #fff;"><?php echo htmlspecialchars($item['brand'] . ' ' . $item['model']); ?></h5>
                    
                    <?php if ($item['review_id']): ?>
                        <p class="reviewed-message">✓ Bạn đã đánh giá sản phẩm này.</p>
                    <?php else: ?>
                        <form action="php/review_handler.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            
                            <div class="rating">
                                <input type="radio" id="star5_<?php echo $item['product_id']; ?>" name="rating" value="5" required/><label for="star5_<?php echo $item['product_id']; ?>"></label>
                                <input type="radio" id="star4_<?php echo $item['product_id']; ?>" name="rating" value="4"/><label for="star4_<?php echo $item['product_id']; ?>"></label>
                                <input type="radio" id="star3_<?php echo $item['product_id']; ?>" name="rating" value="3"/><label for="star3_<?php echo $item['product_id']; ?>"></label>
                                <input type="radio" id="star2_<?php echo $item['product_id']; ?>" name="rating" value="2"/><label for="star2_<?php echo $item['product_id']; ?>"></label>
                                <input type="radio" id="star1_<?php echo $item['product_id']; ?>" name="rating" value="1"/><label for="star1_<?php echo $item['product_id']; ?>"></label>
                            </div>

                            <div class="form-group mt-2">
                                <textarea name="comment" class="form-control" rows="3" placeholder="Viết bình luận của bạn..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-success mt-2">Gửi Đánh Giá</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
