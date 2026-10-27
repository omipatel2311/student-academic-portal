<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>

<h2>User Registration Form</h2>

<form action="process.php" method="POST">
    <label>Full Name:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required minlength="8"><br><br>

    <button type="submit">Register</button>
</form>

</body>
</html>