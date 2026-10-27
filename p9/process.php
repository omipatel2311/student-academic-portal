<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    exit("Invalid request.");
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($name == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || $username == "" || strlen($password) < 8) {
    exit("Invalid input. Please check all fields.");
}

$stmt = $conn->prepare(
    "SELECT id FROM users WHERE email = ? OR username = ?"
);
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    exit("Email or username already exists.");
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO users (username, email, password)
    VALUES (?, ?, ?)"
);

$stmt->bind_param("sss", $username, $email, $hashedPassword);

if ($stmt->execute()) {
    echo "Registration successful!";
} else {
    echo "Registration failed. Please try again.";
}

$stmt->close();
$conn->close();
?>