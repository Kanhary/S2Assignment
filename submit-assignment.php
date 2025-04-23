<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in and is a student
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: login.php");
    exit;
}

// Check if assignment ID is provided
if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$assignment_id = $_GET['id'];
$assignment = getAssignment($assignment_id);

// Check if assignment exists
if(!$assignment) {
    header("Location: index.php");
    exit;
}

// Check if student has already submitted this assignment
if(isSubmitted($_SESSION['user_id'], $assignment_id)) {
    header("Location: view-assignment.php?id=$assignment_id");
    exit;
}

$error = '';
$success = '';

// Process form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'] ?? '';
    
    if(empty($content)) {
        $error = "Please fill in all fields";
    } else {
        $file_name = null;
        
        // Handle file upload if present
        if(isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $allowed = array('pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png');
            $filename = $_FILES['file']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            
            if(!in_array(strtolower($ext), $allowed)) {
                $error = "Invalid file format. Allowed formats: " . implode(', ', $allowed);
            } else {
                $upload_dir = 'uploads/';
                $new_filename = uniqid() . '.' . $ext;
                
                if(move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $new_filename)) {
                    $file_name = $new_filename;
                } else {
                    $error = "Failed to upload file. Please try again.";
                }
            }
        }
        
        if(empty($error)) {
            // Submit the assignment
            if(submitAssignment($_SESSION['user_id'], $assignment_id, $content, $file_name)) {
                $success = "Assignment submitted successfully!";
            } else {
                $error = "Failed to submit assignment. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Assignment - Assignment Collection System</title>
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->

    <style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f5f7fa;
        color: #333;
    }

    main {
        margin-left: 300px;
        width: calc(100% - 300px);
        min-height: 100vh;
        padding: 40px 20px;
        background-color: #f5f7fa;
    }

    .form-container {
        background-color: #fff;
        padding: 30px 35px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(31, 45, 84, 0.26);
        border-left:5px solid  rgb(30, 42, 76);
        max-width: 800px;
        margin: 0 auto;
    }

    .form-container h2 {
        font-size: 28px;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    .form-container h3 {
        font-size: 22px;
        margin-bottom: 5px;
        color: #34495e;
    }

    .due-date {
        color: #e74c3c;
        font-weight: 500;
        margin-bottom: 15px;
    }

    .form-container p {
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #2d3436;
    }

    textarea {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        resize: vertical;
        font-size: 16px;
        font-family: inherit;
        min-height: 180px;
        transition: border-color 0.2s ease-in-out;
    }

    textarea:focus {
        border-color: #3498db;
        outline: none;
    }

    input[type="file"] {
        display: block;
        font-size: 15px;
        padding: 8px 0;
    }

    .help-text {
        font-size: 13px;
        color: #7f8c8d;
    }

    .btn {
        background:rgb(9, 29, 55);
        color: #fff;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease-in-out;
    }

    .btn:hover {
        background-color: #2980b9;
    }

    .error-message {
        background-color: #ffe6e6;
        color: #c0392b;
        padding: 12px 16px;
        margin-bottom: 20px;
        border-radius: 8px;
        border: 1px solid #e74c3c;
    }

    .success-message {
        background-color: #e8f9e8;
        color: #27ae60;
        padding: 12px 16px;
        margin-bottom: 20px;
        border-radius: 8px;
        border: 1px solid #2ecc71;
    }

    .success-message a {
        color: #2980b9;
        text-decoration: underline;
        font-weight: 500;
    }

    @media screen and (max-width: 768px) {
        main {
            margin-left: 0;
            width: 100%;
            padding: 20px;
        }

        .form-container {
            padding: 25px 20px;
        }
    }
</style>

</head>
<body>

        <?php include 'includes/sidebar.php'; ?>
        
        <main>
            <div class="form-container">
                <h2>Submit Assignment</h2>
                <h3><?php echo htmlspecialchars($assignment['title']); ?></h3>
                <p class="due-date">Due: <?php echo htmlspecialchars($assignment['due_date']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
                
                <?php if(!empty($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($success)): ?>
                    <div class="success-message">
                        <?php echo $success; ?>
                        <p><a href="view-assignment.php?id=<?php echo $assignment_id; ?>">View Assignment</a></p>
                    </div>
                <?php else: ?>
                    <form action="submit-assignment.php?id=<?php echo $assignment_id; ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="content">Your Answer</label>
                            <textarea id="content" name="content" rows="10" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="file">Attachment (Optional)</label>
                            <input type="file" id="file" name="file">
                            <p class="help-text">Allowed formats: pdf, doc, docx, txt, jpg, jpeg, png</p>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn">Submit Assignment</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </main>
        
        <!-- <?php include 'includes/footer.php'; ?> -->

</body>
</html>

