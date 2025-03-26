<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$assignment_id = $_GET['id'];
$assignment = getAssignmentById($assignment_id);

if (!$assignment) {
    echo "Assignment not found.";
    exit;
}

$assignment['description'] = htmlspecialchars($assignment['description']);

// Check if the student has submitted the assignment
$submittedFile = null;
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'student') {
    $submittedFile = getSubmittedFile($_SESSION['user_id'], $assignment_id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assignment</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
    <?php include 'includes/sidebar.php'; ?>

    <div class="assignment-detail">
        <h2><?php echo htmlspecialchars($assignment['title']); ?></h2>
        <p><strong>Description:</strong></p>
        <p><?php echo nl2br($assignment['description']); ?></p>
        <p><strong>Due Date:</strong> <?php echo htmlspecialchars($assignment['due_date']); ?></p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['user_role'] == 'teacher'): ?>
                <div class="assignment-actions">
                    <a href="edit-assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-primary">Edit Assignment</a>
                    <a href="delete-assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this assignment?');">Delete</a>
                </div>
            <?php elseif ($_SESSION['user_role'] == 'student'): ?>
                <p><strong>Status:</strong> 
                    <?php if (isSubmitted($_SESSION['user_id'], $assignment['id'])): ?>
                        <span class="status submitted">Submitted</span>
                    <?php else: ?>
                        <span class="status pending">Pending</span>
                    <?php endif; ?>
                </p>

                <?php if ($submittedFile): ?>
                    <p><strong>Submitted File:</strong></p>
                    <p><a href="uploads/<?php echo $submittedFile['file_name']; ?>" target="_blank" class="btn btn-download">Download File</a></p>
                <?php endif; ?>

                <?php if (!isSubmitted($_SESSION['user_id'], $assignment['id'])): ?>
                    <a href="submit-assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-primary">Submit Assignment</a>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="welcome">
                <h2>Please Log In to View Assignments</h2>
                <a href="login.php" class="btn btn-primary">Login</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

</body>
</html>
