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

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total records
$totalResult = $conn->query("SELECT COUNT(*) as total FROM blogs");
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);

// Fetch paginated blogs
$sql = "SELECT * FROM blogs ORDER BY created_at DESC LIMIT $offset, $limit";
$result = $conn->query($sql);

$blogs = [];
while ($row = $result->fetch_assoc()) {
    $blogs[] = $row;
}

echo json_encode([
    'blogs' => $blogs,
    'totalPages' => $totalPages
]);
?>
