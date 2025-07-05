<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: signin.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);

    $conn = new mysqli('localhost', 'root', '', 'blog_post');
    if ($conn->connect_error) {
        die("Connection failed.");
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}

header("Location: users.php");
exit;
