<?php
// BƯỚC 1: KHAI BÁO CÁC CLASS SẼ SỬ DỤNG
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// BƯỚC 2: NẠP FILE AUTOLOAD CỦA COMPOSER (Cách chuẩn nhất)
// Đường dẫn này giả định file contact_handler.php nằm trong thư mục 'php'
require '../vendor/autoload.php';

// BƯỚC 3: LẤY DỮ LIỆU TỪ FORM
$name = $_POST['name'] ?? 'Không có tên';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? 'Không có SĐT';
$subject = $_POST['subject'] ?? 'Không có chủ đề';
$message = $_POST['message'] ?? 'Không có nội dung';

// Kiểm tra email có hợp lệ không
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Chuyển hướng về trang liên hệ với thông báo lỗi
    header('Location: ../lien-he.php?status=invalid_email');
    exit();
}

// Khởi tạo đối tượng PHPMailer
$mail = new PHPMailer(true);

try {
    // Cấu hình máy chủ SMTP của Gmail
    // $mail->SMTPDebug = 2; // Bật dòng này để xem log lỗi chi tiết khi cần
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'bangqs102@gmail.com'; // Email của bạn dùng để gửi
    $mail->Password   = 'fzdb xbhv kydo vrsx'; // Mật khẩu ứng dụng của bạn
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Đổi thành SMTPS cho bảo mật tốt hơn
    $mail->Port       = 465;                      // Port cho SMTPS là 465
    $mail->CharSet    = 'UTF-8';

    // BƯỚC 4: CẤU HÌNH NGƯỜI GỬI VÀ NGƯỜI NHẬN (ĐÃ SỬA LỖI)

    // Người gửi: LUÔN LÀ EMAIL CỦA BẠN
    // Tên người gửi sẽ là tên của khách hàng để bạn dễ nhận biết
    $mail->setFrom('bangqs102@gmail.com', $name);

    // Người nhận: Vẫn là email của bạn (chủ cửa hàng)
    $mail->addAddress('bangqs102@gmail.com', 'Admin ShopXe');

    // Reply-To: KHI BẠN NHẤN "TRẢ LỜI", EMAIL SẼ TỰ ĐỘNG GỬI ĐẾN KHÁCH HÀNG
    $mail->addReplyTo($email, $name);

    // BƯỚC 5: NỘI DUNG EMAIL
    $mail->isHTML(true);
    $mail->Subject = "[ShopXe Contact] - " . htmlspecialchars($subject);
    
    // Tạo nội dung email để gửi cho admin
    $mail->Body = "
        <h2>Bạn có tin nhắn mới từ khách hàng:</h2>
        <p><strong>Họ và tên:</strong> " . htmlspecialchars($name) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
        <p><strong>Số điện thoại:</strong> " . htmlspecialchars($phone) . "</p>
        <hr>
        <p><strong>Nội dung tin nhắn:</strong></p>
        <p>" . nl2br(htmlspecialchars($message)) . "</p>
    ";

    // Gửi email
    $mail->send();
    
    // Gửi thành công, chuyển hướng về trang liên hệ với thông báo
    header('Location: ../lien-he.php?status=success');
    exit();

} catch (Exception $e) {
    // Có lỗi, chuyển hướng về trang liên hệ với thông báo lỗi
    // Ghi lại lỗi để bạn xem (quan trọng khi debug):
    // error_log("Mailer Error: {$mail->ErrorInfo}");
    header('Location: ../lien-he.php?status=error');
    exit();
}
?>

