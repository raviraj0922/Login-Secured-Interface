<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// DB Config
$host = "localhost";
$dbname = "blog_post";
$username = "root";
$password = "";

// Create DB connection
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database error.");
}

// Get and sanitize email
$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email address.";
    exit;
}

// Check for duplicate
$check = $conn->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo "You are already subscribed.";
    $check->close();
    exit;
}
$check->close();

// Insert new subscription
$stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
$stmt->bind_param("s", $email);
if ($stmt->execute()) {
    // Send confirmation email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'skyworldconsultingcompany.com'; // e.g., smtp.example.com
        $mail->SMTPAuth   = true;
        $mail->Username   = 'no-reply@skyworldconsultingcompany.com';
        $mail->Password   = 'A[T_ZghVn;Dt';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('no-reply@skyworldconsultingcompany.com', 'Skyworld Consulting Company');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Thanks for subscribing!';
        $mail->Body    = "<p>Hi there,</p><p>Thanks for subscribing to our newsletter. You'll hear from us soon!</p><p>- Team <br>SWCC</p>";

        $mail->send();
        echo "Subscribed successfully!";
    } catch (Exception $e) {
        echo "Subscribed, but email not sent.";
    }
} else {
    echo "Failed to subscribe.";
}
$stmt->close();
$conn->close();
?>
