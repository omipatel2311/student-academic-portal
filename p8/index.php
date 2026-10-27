
<?php
require_once "db.php";

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM students");
    $stmt->execute();
    $result = $stmt->fetch();

    echo "<h2>StudentHub Database Connected Successfully!</h2>";
    echo "<p>Database: studenthub</p>";
    echo "<p>Total Students: " . htmlspecialchars((string)$result["total"]) . "</p>";

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo "Unable to retrieve student records.";
}
?>
