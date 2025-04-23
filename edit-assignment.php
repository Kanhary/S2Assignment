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
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            display: flex;
        }

        .assignment-form {
            margin-left: 570px;
            margin-top: 40px;
            padding: 40px 30px;
            background-color: #fff;
            width: 50%;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #1e2a4c;
            height: 600px;
        }

        h2 {
            margin-top: 0;
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 30px;
            /* border-bottom: 2px solid #3498db; */
            padding-bottom: 10px;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            margin-top: 20px;
        }

        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            margin-top: 4px;
        }

        textarea {
            resize: vertical;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 15px;
            font-weight: 500;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 25px;
            margin-right: 12px;
            transition: background-color 0.3s ease;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background-color: #3498db;
            color: #fff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-secondary {
            background-color: #bdc3c7;
            color: #fff;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #95a5a6;
        }

        .error {
            color: #e74c3c;
            background: #fdecea;
            border: 1px solid #e0b4b4;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .assignment-form {
                margin-left: 0;
                margin-top: 20px;
                width: 100%;
                padding: 30px 20px;
            }

            h2 {
                font-size: 24px;
            }

            .btn {
                padding: 9px 18px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>


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


</body>
</html>
