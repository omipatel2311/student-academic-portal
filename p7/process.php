<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = trim($_POST["course"] ?? "");

    $errors = [];

    if ($name == "") {
        $errors[] = "Name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email.";
    }

    if (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $errors[] = "Mobile number must contain 10 digits.";
    }

    if ($course == "") {
        $errors[] = "Please select a course.";
    }

    if (empty($errors)) {
        $file = fopen("data.csv", "a");

        if ($file !== false) {
            fputcsv($file, [$name, $email, $mobile, $course]);
            fclose($file);

            echo "<h2>Registration Successful!</h2>";
            echo "<p>Your data has been saved.</p>";
        } else {
            echo "Error: Unable to save data.";
        }
    } else {
        echo "<h2>Please correct these errors:</h2>";

        foreach ($errors as $error) {
            echo "<p>" . htmlspecialchars($error) . "</p>";
        }
    }

    echo '<p><a href="index.php">Go Back</a></p>';

} else {
    echo "Invalid request.";
}
?>