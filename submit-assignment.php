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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
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
        
        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>

