<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: index.php");
    exit;
}

// Check if an assignment ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$assignment_id = $_GET['id'];
$assignment = getAssignmentById($assignment_id);

// Redirect if assignment not found
if (!$assignment) {
    echo "Assignment not found.";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];

    // Basic validation
    if (empty($title) || empty($description) || empty($due_date)) {
        $error = "All fields are required.";
    } else {
        // Update assignment in the database
        $updated = updateAssignment($assignment_id, $title, $description, $due_date);
        if ($updated) {
            header("Location: view-assignment.php?id=$assignment_id&updated=true");
            exit;
        } else {
            $error = "Failed to update the assignment.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assignment</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
    <?php include 'includes/sidebar.php'; ?>

    <div class="assignment-form">
        <h2>Edit Assignment</h2>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($assignment['title']); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($assignment['description']); ?></textarea>

            <label for="due_date">Due Date:</label>
            <input type="date" id="due_date" name="due_date" value="<?php echo htmlspecialchars($assignment['due_date']); ?>" required>

            <button type="submit" class="btn btn-primary">Update Assignment</button>
            <a href="view-assignment.php?id=<?php echo $assignment_id; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <!-- <?php include 'includes/footer.php'; ?> -->
</div>

</body>
</html>
