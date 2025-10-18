<?php
session_start();
require_once('../includes/db_connect.php'); // Adjust path if needed

// 1. Validate the request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// 2. Sanitize and retrieve POST data
$user_id    = $_SESSION['user_id'];
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$order_id   = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$rating     = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
$comment    = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS);

// 3. Check for valid data
if (!$product_id || !$order_id || !$rating || $rating < 1 || $rating > 5) {
    // Redirect back with an error if data is invalid
    header("Location: ../order_success.php?order_id=$order_id&review_status=error");
    exit();
}

// 4. Prevent duplicate reviews for the same product in the same order
$checkStmt = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND product_id = ? AND order_id = ?");
$checkStmt->bind_param("iii", $user_id, $product_id, $order_id);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    // Already reviewed, just redirect back
    header("Location: ../order_success.php?order_id=$order_id");
    exit();
}
$checkStmt->close();


// 5. Insert the new review into the database
$insertStmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, order_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
$insertStmt->bind_param("iiiis", $user_id, $product_id, $order_id, $rating, $comment);

if ($insertStmt->execute()) {
    // Success! Redirect back with a success message.
    header("Location: ../order_success.php?order_id=$order_id&review_status=success");
} else {
    // Failure! Redirect back with an error message.
    header("Location: ../order_success.php?order_id=$order_id&review_status=error");
}

$insertStmt->close();
$conn->close();
exit();