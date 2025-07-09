<?php
header('Content-Type: application/json');

$host = 'localhost';
$db = 'blog_post';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM blogs WHERE id = $id LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(['error' => 'Blog not found']);
}
?>
