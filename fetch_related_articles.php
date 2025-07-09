<?php
header('Content-Type: application/json');

$host = 'localhost';
$db = 'blog_post';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$currentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$currentId) {
    echo json_encode([]);
    exit;
}

// Step 1: Get the category of the current blog
$stmt = $conn->prepare("SELECT category FROM blogs WHERE id = ?");
$stmt->bind_param("i", $currentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode([]);
    exit;
}

$currentBlog = $result->fetch_assoc();
$currentCategory = $currentBlog['category'];

// ✅ Step 2: Get related blogs in the same category (excluding current blog) + include views
$stmt = $conn->prepare("SELECT id, title, image, created_at, views FROM blogs WHERE category = ? AND id != ? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param("si", $currentCategory, $currentId);
$stmt->execute();
$relatedResult = $stmt->get_result();

$relatedBlogs = [];
while ($row = $relatedResult->fetch_assoc()) {
    $relatedBlogs[] = $row;
}

echo json_encode($relatedBlogs);
?>
