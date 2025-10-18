<?php
// Nhúng header
include 'includes/header.php';
// Nhúng file kết nối CSDL
require 'includes/db_connect.php';

// Lấy ID bài viết từ URL một cách an toàn
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Nếu không có ID, chuyển hướng về trang tin tức
if ($article_id <= 0) {
    header('Location: tin-tuc.php');
    exit();
}

// Sử dụng prepared statement để chống SQL Injection
$sql = "SELECT title, content, image_url, author, created_at FROM articles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

// Nếu không tìm thấy bài viết, hiển thị thông báo
if (!$article) {
    echo "<main class='article-detail-page container'><p>Bài viết không tồn tại.</p></main>";
    include 'includes/footer.php';
    exit();
}
?>

<main class="article-detail-page">
    <div class="container">
        <article class="article-full-content">
            <header class="article-header">
                <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                <div class="article-meta">
                    <span><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($article['author']); ?></span>
                    <span><i class="fa-solid fa-calendar-days"></i> <?php echo date('d/m/Y', strtotime($article['created_at'])); ?></span>
                </div>
            </header>

            <figure class="article-image-full">
                <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
            </figure>

            <section class="article-body">
                <?php
                    // Hiển thị nội dung, chuyển đổi ký tự xuống dòng thành thẻ <br>
                    echo nl2br(htmlspecialchars($article['content']));
                ?>
            </section>
        </article>
    </div>
</main>

<?php
// Nhúng footer
include 'includes/footer.php';
?>