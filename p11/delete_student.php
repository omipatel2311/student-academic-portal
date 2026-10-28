<?php
require_once "db.php";

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? 'delete';

if ($id <= 0) {
    set_flash('danger', "Invalid student ID.");
    header("Location: index.php");
    exit();
}

if ($action === 'toggle_status') {
    // Advanced Extension: Soft delete / Status toggle
    $stmt = $conn->prepare("UPDATE students SET status = IF(status = 'Active', 'Inactive', 'Active') WHERE student_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        set_flash('success', "Student enrollment status toggled successfully.");
    } else {
        set_flash('danger', "Failed to toggle status: " . $conn->error);
    }
    $stmt->close();
} else {
    // Standard Permanent DELETE operation
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            set_flash('success', "Student record #{$id} deleted successfully.");
        } else {
            set_flash('danger', "Student record not found or already deleted.");
        }
    } else {
        set_flash('danger', "Failed to delete student: " . $conn->error);
    }
    $stmt->close();
}

$conn->close();
header("Location: index.php");
exit();
?>
