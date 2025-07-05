<?php
session_start();

// Database connection
$host = 'localhost';
$db   = 'blog_post';
$user = 'root';
$pass = ''; 
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

// Handle POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"], $_POST["password"])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Secure prepared statement
    $stmt = $conn->prepare("SELECT id, name, role, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $name, $role, $hashed_password);
        $stmt->fetch();

        // Compare hashed passwords (your DB uses MD5)
        if (md5($password) === $hashed_password) {
            // Store all info in session
            $_SESSION["user_id"] = $user_id;
            $_SESSION["email"] = $email;
            $_SESSION["name"] = $name;
            $_SESSION["role"] = $role;

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "❌ Invalid password.";
        }
    } else {
        $error = "❌ No account found with that email.";
    }

    $stmt->close();
}

$conn->close();
?>
