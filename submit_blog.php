<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: signin.html");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "blog_post");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize & validate inputs
$title = trim($_POST['title']);
$category = trim($_POST['category']);
$description = $_POST['description']; // from CKEditor
$user_id = $_SESSION['user_id'];
$image_url = trim($_POST['image_url']);
$image_path = '';

if (empty($title) || empty($category) || empty($description)) {
    die("Please fill in all fields.");
}

// ✅ Check for uploaded file
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = time() . "_" . basename($_FILES['image']['name']);
    $fileDest = $uploadDir . $fileName;

    // Move file
    if (move_uploaded_file($fileTmpPath, $fileDest)) {
        $image_path = $fileDest;
    }
}

// ✅ Fallback to image URL if no upload
if (empty($image_path) && !empty($image_url)) {
    $image_path = $image_url;
}

// Prepared statement
$stmt = $conn->prepare("INSERT INTO blogs (title, category, description, user_id, image) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssiss", $title, $category, $description, $user_id, $image_path);

if ($stmt->execute()) {
    header("Location: add-blog.php?success=1");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
