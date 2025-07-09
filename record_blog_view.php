<?php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "blog_post";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing blog ID']);
    exit;
}

$blogId = intval($_GET['id']);

// Increment views
$stmt = $conn->prepare("UPDATE blogs SET views = views + 1 WHERE id = ?");
$stmt->bind_param("i", $blogId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
