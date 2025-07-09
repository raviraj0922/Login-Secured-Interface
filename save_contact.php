<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Database config
    $host = "localhost";
    $username = "root";     // Change this
    $password = "";         // Change this
    $dbname = "blog_post";

    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        http_response_code(500);
        echo "Database connection failed: " . $conn->connect_error;
        exit;
    }

    // Get and sanitize POST data
    $name = $conn->real_escape_string($_POST["name"]);
    $phone = $conn->real_escape_string($_POST["phone"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $address = $conn->real_escape_string($_POST["address"]);
    $message = $conn->real_escape_string($_POST["message"]);

    // Insert query
    $sql = "INSERT INTO contact_messages (name, phone, email, address, message)
            VALUES ('$name', '$phone', '$email', '$address', '$message')";

    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        http_response_code(500);
        echo "Error: " . $conn->error;
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method.";
}
?>
