<?php
// write_review.php
session_start();
require_once 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
if ($product_id === 0) die('Sản phẩm không hợp lệ.');

// Xử lý khi form được gửi đi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
    $stmt->execute();
    $stmt->close();
    header("Location: my_orders.php"); // Quay lại trang lịch sử đơn hàng
    exit();
}
include 'includes/header.php';
?>
<style>
/* CSS cho ngôi sao đánh giá */
.rating { display: flex; flex-direction: row-reverse; justify-content: center; }
.rating > input{ display:none; }
.rating > label { position: relative; width: 1.1em; font-size: 3rem; color: #FFD700; cursor: pointer; }
.rating > label::before{ content: "\2605"; position: absolute; opacity: 0; }
.rating > label:hover:before, .rating > label:hover ~ label:before { opacity: 1 !important; }
.rating > input:checked ~ label:before{ opacity:1; }
.rating:hover > input:checked ~ label:before{ opacity: 0.4; }
</style>
<div class="container my-5">
    <h1>Viết Đánh Giá Sản Phẩm</h1>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Xếp hạng của bạn</label>
            <div class="rating">
              <input type="radio" name="rating" value="5" id="5"><label for="5">☆</label>
              <input type="radio" name="rating" value="4" id="4"><label for="4">☆</label>
              <input type="radio" name="rating" value="3" id="3"><label for="3">☆</label>
              <input type="radio" name="rating" value="2" id="2"><label for="2">☆</label>
              <input type="radio" name="rating" value="1" id="1" required><label for="1">☆</label>
            </div>
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Bình luận của bạn</label>
            <textarea name="comment" id="comment" class="form-control bg-dark text-white" rows="5"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi Đánh Giá</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>