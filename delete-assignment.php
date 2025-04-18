<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: index.php");
    exit;
}

// Check if assignment ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$assignment_id = $_GET['id'];

// Check if assignment exists
$assignment = getAssignmentById($assignment_id);
if (!$assignment) {
    echo "Assignment not found.";
    exit;
}

// Delete submitted files related to this assignment
$submittedFiles = getSubmittedFilesByAssignmentId($assignment_id);
foreach ($submittedFiles as $file) {
    $filePath = 'uploads/' . $file['file_name'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

try {
    // Use global $pdo
    global $pdo;

    // Begin transaction
    $pdo->beginTransaction();

    // Delete submissions
    $stmtSub = $pdo->prepare("DELETE FROM submissions WHERE assignment_id = ?");
    $stmtSub->execute([$assignment_id]);

    // Delete assignment
    $stmt = $pdo->prepare("DELETE FROM assignments WHERE id = ?");
    $stmt->execute([$assignment_id]);

    // Commit transaction
    $pdo->commit();

    header("Location: index.php?message=Assignment+deleted+successfully");
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Error deleting assignment: " . $e->getMessage();
}
?>
