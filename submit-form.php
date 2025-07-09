<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// DB config
$host = "localhost";
$dbname = "blog_post";
$username = "root";
$password = "";

// Connect DB
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Upload helper
function saveUploadedFile($fileInput, $uploadDir = "uploads/") {
    if (isset($_FILES[$fileInput]) && $_FILES[$fileInput]['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES[$fileInput]['name'], PATHINFO_EXTENSION);
        $newName = date("Ymd_His") . '_' . uniqid() . '.' . $ext;
        $targetPath = $uploadDir . $newName;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        if (move_uploaded_file($_FILES[$fileInput]['tmp_name'], $targetPath)) {
            return $targetPath;
        }
    }
    return null;
}

// Collect inputs
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$dob = $_POST['dob'] ?? '';
$phone = $_POST['phone'] ?? '';
$gender = $_POST['gender'] ?? '';
$position_applied = $_POST['position_applied'] ?? '';
$linkedin_url = $_POST['linkedin_url'] ?? '';
$current_address = $_POST['current_address'] ?? '';
$permanent_address = $_POST['permanent_address'] ?? '';

$resume_path = saveUploadedFile('resume');
$cover_letter_path = saveUploadedFile('cover_letter');

if ($resume_path && $cover_letter_path) {
    $stmt = $conn->prepare("INSERT INTO registrations (
        first_name, last_name, email, dob, phone, gender, position_applied, linkedin_url,
        current_address, permanent_address, resume_path, cover_letter_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssssssss",
        $first_name, $last_name, $email, $dob, $phone, $gender, $position_applied,
        $linkedin_url, $current_address, $permanent_address, $resume_path, $cover_letter_path
    );

    if ($stmt->execute()) {
        // ✅ Send Email via SMTP
        $mail = new PHPMailer(true);
        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host       = 'skyworldconsultingcompany.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'no-reply@skyworldconsultingcompany.com';
            $mail->Password   = 'A[T_ZghVn;Dt';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Email content
            $mail->setFrom('no-reply@skyworldconsultingcompany.com', 'SWCC HR');
            $mail->addAddress($email, $first_name . ' ' . $last_name);

            $mail->isHTML(true);
            $mail->Subject = 'Application Received - SWCC';
            $mail->Body = "
                <p>Dear {$first_name} {$last_name},</p>
                <p>Thank you for applying for the position of <strong>{$position_applied}</strong>.</p>
                <p>We have received your application and our team will contact you shortly.</p>
                <br>
                <p>Regards,<br>SWCC HR Team</p>
            ";

            if ($mail->send()) {
                header("Location: registration.html?success=1");
                exit();
            } else {
                echo "Application saved but email failed: {$mail->ErrorInfo}";
            }

        } catch (Exception $e) {
            echo "Application saved but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error saving data: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "File upload failed.";
}

$conn->close();
?>
