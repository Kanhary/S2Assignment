<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';
$classes = getTeacherClasses($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $due_date = $_POST['due_date'] ?? '';
    $class_id = $_POST['class_id'] ?? '';

    if (empty($title) || empty($description) || empty($due_date) || empty($class_id)) {
        $error = "Please fill in all fields.";
    } else {
        if (createAssignment($title, $description, $due_date, $class_id, $_SESSION['user_id'])) {
            $success = "Assignment created successfully!";
        } else {
            $error = "Failed to create assignment. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Assignment - Assignment Collection System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fc;
        }

        main {
            margin-left: 250px;
            padding: 40px;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .form-container {
            max-width: 700px;
            background-color: #ffffff;
            margin: 0 auto;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 26px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        input[type="text"],
        input[type="datetime-local"],
        textarea,
        select {
            width: 100%;
            padding: 10px 14px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #5c9ded;
            outline: none;
        }

        textarea {
            resize: vertical;
        }

        .btn {
            background-color: #5c9ded;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }

        .btn:hover {
            background-color: #4a89dc;
        }

        .error-message,
        .success-message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
            text-align: center;
        }

        .error-message {
            background-color: #ffe0e0;
            color: #d64545;
        }

        .success-message {
            background-color: #e0f7e9;
            color: #2e7d50;
        }

        @media screen and (max-width: 768px) {
            main {
                margin-left: 0;
                padding: 20px;
            }

            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <?php include 'includes/sidebar.php'; ?>

    <main>
        <div class="form-container">
            <h2>Create New Assignment</h2>

            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="create-assignment.php" method="post">
                <div class="form-group">
                    <label for="title">Assignment Title</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" required></textarea>
                </div>

                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="datetime-local" id="due_date" name="due_date" required>
                </div>

                <div class="form-group">
                    <label for="class_id">Class</label>
                    <select id="class_id" name="class_id" required>
                        <option value="">Select Class</option>
                        <?php foreach($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>">
                                <?php echo htmlspecialchars($class['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Create Assignment</button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
