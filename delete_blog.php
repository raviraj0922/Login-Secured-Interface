<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blog_id'])) {
    $conn = new mysqli("localhost", "root", "", "blog_post");
    if ($conn->connect_error) {
        die("Connection failed");
    }

    $blog_id = intval($_POST['blog_id']);
    $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}

header("Location: modify-blog.php");
exit;
?>