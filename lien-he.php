<?php include 'includes/header.php'; ?>

<main class="contact-page">
    <section class="hero-section contact-hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('assets/images/contact-hero.jpg');">
        <div class="hero-content">
            <h1>Kết Nối Với Chúng Tôi</h1>
            <p>Chúng tôi luôn sẵn sàng lắng nghe và giải đáp mọi thắc mắc của bạn.</p>
        </div>
    </section>
    <form action="php/contact_handler.php" method="post">
    </form>

    <section class="contact-content">
        <div class="container">
            <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') {
                    echo '<div style="color: green; text-align: center; font-weight: bold; padding: 10px; border: 1px solid green; margin-bottom: 20px;">Cảm ơn bạn! Tin nhắn đã được gửi thành công.</div>';
                } elseif ($_GET['status'] == 'error') {
                    echo '<div style="color: red; text-align: center; font-weight: bold; padding: 10px; border: 1px solid red; margin-bottom: 20px;">Có lỗi xảy ra. Vui lòng thử lại.</div>';
                }
            }
            ?>
            <div class="contact-grid">
                <div class="contact-info">
                    <h3>Thông Tin Liên Hệ</h3>
                    <p>Hãy liên hệ với chúng tôi qua các kênh dưới đây...</p>
                    <ul>
                        <li><i class="fa-solid fa-location-dot"></i> 123 Đường ABC, Quận 1, TP. Hồ Chí Minh</li>
                        <li><i class="fa-solid fa-phone"></i> (028) 3812 3456</li>
                        <li><i class="fa-solid fa-envelope"></i> contact@shopxe.com</li>
                        <li><i class="fa-solid fa-clock"></i> Thứ 2 - Chủ Nhật: 8:00 - 20:00</li>
                    </ul>
                </div>

                <div class="contact-form-container">
                    <h3>Gửi Tin Nhắn Cho Chúng Tôi</h3>
                    <form action="contact_handler.php" method="POST" class="contact-form">
                        <input type="text" name="name" placeholder="Họ và Tên" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="tel" name="phone" placeholder="Số điện thoại (không bắt buộc)">
                        <input type="text" name="subject" placeholder="Chủ đề" required>
                        <textarea name="message" placeholder="Nội dung tin nhắn" rows="6" required></textarea>
                        <button type="submit" class="btn-submit">Gửi Tin Nhắn</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <section class="map-section">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.493922712496!2d106.6958432758852!3d10.77320098937503!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f38558b5357%3A0x1131a34353f86466!2zQ2jhu6MgQuG6v24gVGjDoG5o!5e0!3m2!1svi!2s!4v1728987941551!5m2!1svi!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>    </section>
</main>

<?php include 'includes/footer.php'; ?>