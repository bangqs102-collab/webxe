<?php include 'includes/header.php'; ?>
<?php require_once 'includes/db_connect.php'; ?>

<!-- CSS để làm nổi bật sản phẩm hết hàng và định dạng văn bản -->
<style>
    .product-card.sold-out {
        position: relative;
        opacity: 0.7;
    }
    .product-card.sold-out::after {
        content: 'HẾT HÀNG';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-15deg);
        background-color: rgba(220, 53, 69, 0.85);
        color: white;
        padding: 10px 25px;
        font-weight: bold;
        font-size: 1.5rem;
        border-radius: 5px;
        z-index: 10;
        pointer-events: none;
    }
    .product-card.sold-out .card-img-top {
        filter: grayscale(90%);
    }
    .quantity-text {
        color: black !important;
    }
</style>

<div class="container my-5">
    <h1 class="section-title text-center mb-5">Khám Phá Xe Của Chúng Tôi</h1>

    <div class="row g-4">
        <?php
        // THAY ĐỔI: Sắp xếp để sản phẩm hết hàng xuống cuối, xe mới nhất lên đầu
        $sql = "SELECT id, brand, model, price, image_url, quantity FROM cars ORDER BY (quantity > 0) DESC, id DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
        ?>
            <div class="col-md-4">
                <div class="card product-card h-100 <?php echo ($row['quantity'] <= 0) ? 'sold-out' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['brand']); ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?></h5>
                        <p class="card-text price">$<?php echo number_format($row['price']); ?></p>

                        <p class="card-text quantity-text small mb-3">Số lượng còn lại: <?php echo $row['quantity']; ?></p>

                        <?php if ($row['quantity'] > 0): ?>
                            <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-light mt-auto">Xem Chi Tiết</a>
                        <?php else: ?>
                            <button class="btn btn-secondary mt-auto" disabled>Hết Hàng</button>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        <?php
            }
        } else {
            echo "<p class='text-light'>Hiện chưa có xe nào được bán.</p>";
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

