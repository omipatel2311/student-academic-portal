<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>Student Registration</h1>

        <form action="process.php" method="POST">
            <label>Full Name:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Mobile Number:</label>
            <input type="text" name="mobile" required>

            <label>Course:</label>
            <select name="course" required>
                <option value="">Select Course</option>
                <option value="Computer Engineering">Computer Engineering</option>
                <option value="IT">Information Technology</option>
                <option value="Other">Other</option>
            </select>

            <button type="submit">Register</button>
        </form>
    </div>

</body>
</html>