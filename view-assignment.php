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
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->

    <!-- Inside your <head> tag -->
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        background-color: #eef2f7;
    }

    .assignment-detail {
        margin-left: 550px;
        padding: 40px;
        width: 50%;
        height: 600px;
        /* width: calc(100% - 250px); */
        background-color: #ffffff;
        border-radius: 12px;
        margin-top: 30px;
        margin-bottom: 30px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        transition: margin-left 0.3s ease;
        box-shadow: 0 4px 20px rgba(31, 45, 84, 0.26);
        border-left:5px solid  rgb(30, 42, 76);
    }

    h2 {
        margin-top: 0;
        font-size: 28px;
        color: #2c3e50;
        /* border-bottom: 2px solid #3498db; */
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    p {
        line-height: 1.8;
        font-size: 16px;
        color: #555;
        padding: 10px 0px;
    }

    strong {
        color: #2c3e50;
    }

    .assignment-actions {
        margin-top: 25px;
    }

    .assignment-actions .btn {
        text-decoration: none;
        padding: 10px 18px;
        border-radius: 8px;
        margin-right: 12px;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-block;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.05);
    }

    .btn-primary {
        background-color: #3498db;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #2980b9;
    }

    .btn-danger {
        background-color: #e74c3c;
        color: #fff;
    }

    .btn-danger:hover {
        background-color: #c0392b;
    }

    .btn-download {
        background:rgb(9, 29, 55);
        color: #fff;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.3s ease;
        display: inline-block;
        margin-top: 10px;
    }

    .btn-download:hover {
        background:rgba(9, 29, 55, 0.7);    
    }

    .status {
        padding: 6px 14px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: bold;
        display: inline-block;
        margin-top: 5px;
    }

    .submitted {
        background-color: #27ae60;
        color: white;
    }

    .pending {
        background-color: #f39c12;
        color: white;
    }

    .welcome {
        text-align: center;
        margin-top: 80px;
    }

    .welcome h2 {
        color: #555;
        font-size: 24px;
        margin-bottom: 15px;
    }

    .welcome .btn-primary {
        margin-top: 15px;
        display: inline-block;
    }

    @media (max-width: 768px) {
        .assignment-detail {
            margin-left: 0;
            width: 100%;
            padding: 25px;
            margin-top: 20px;
        }

        h2 {
            font-size: 24px;
        }

        .assignment-actions .btn,
        .btn-download {
            font-size: 14px;
            padding: 9px 14px;
        }

        .welcome h2 {
            font-size: 20px;
        }
    }
</style>


</head>
<body>


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




</body>
</html>
