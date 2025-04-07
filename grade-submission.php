<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in and is a teacher
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'teacher') {
    header("Location: login.php");
    exit;
}

// Check if submission ID is provided
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view-submissions.php");
    exit;
}

$submission_id = $_GET['id'];

// Get submission details
$stmt = $pdo->prepare("
    SELECT s.*, a.title as assignment_title, a.id as assignment_id, 
           u.name as student_name, u.id as student_id
    FROM submissions s
    JOIN assignments a ON s.assignment_id = a.id
    JOIN users u ON s.student_id = u.id
    WHERE s.id = ? AND a.teacher_id = ?
");
$stmt->execute([$submission_id, $_SESSION['user_id']]);
$submission = $stmt->fetch();

// Check if submission exists and belongs to a class taught by this teacher
if(!$submission) {
    header("Location: view-submissions.php");
    exit;
}

$error = '';
$success = '';

// Process grading form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $grade = $_POST['grade'] ?? '';
    $feedback = $_POST['feedback'] ?? '';
    
    if(!is_numeric($grade) || $grade < 0 || $grade > 100) {
        $error = "Grade must be a number between 0 and 100";
    } else {
        // Update the submission with grade and feedback
        if(gradeSubmission($submission_id, $grade, $feedback)) {
            $success = "Submission graded successfully!";
            
            // Refresh submission data
            $stmt->execute([$submission_id, $_SESSION['user_id']]);
            $submission = $stmt->fetch();
        } else {
            $error = "Failed to grade submission. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Submission - Assignment Collection System</title>
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
    <style>

        .container {
            width: 100%;
            display: flex;
            min-height: 100vh;
            font-family: 'Roboto', sans-serif;
            color: #333;
        }
        main {
            flex: 1;
            padding: 40px;
            /* background-color: #f4f7fb; */
            margin-left: 260px; /* Adjust based on sidebar width */
            box-sizing: border-box;
        }
        .back-link {
            margin-bottom: 20px;
        }
        .btn-small {
            background-color: #3c8dbc;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 14px;
            display: inline-block;
        }
        .btn-small:hover {
            background-color: #367fa9;
        }
        h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .submission-info {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .submission-info h3 {
            font-size: 22px;
            color: #333;
            margin-bottom: 10px;
        }
        .submission-info p {
            font-size: 16px;
            color: #666;
            margin: 5px 0;
        }
        .submission-content {
            margin-top: 20px;
        }
        .content-box {
            background-color: #fff;
            border: 1px solid #e1e1e1;
            padding: 15px;
            border-radius: 8px;
            font-family: "Courier New", Courier, monospace;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin-bottom: 20px;
        }
        .submission-file {
            margin-top: 20px;
        }
        .file-link {
            color: #3c8dbc;
            text-decoration: none;
        }
        .file-link:hover {
            text-decoration: underline;
        }
        .grading-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .grading-form h4 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-size: 16px;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
        }
        .form-group button {
            background-color: #3c8dbc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #367fa9;
        }
        .error-message, .success-message {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main>
            <div class="back-link">
                <a href="view-submissions.php" class="btn-small">Back to Submissions</a>
            </div>
            
            <h2>Grade Submission</h2>
            
            <?php if(!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="submission-info">
                <h3><?php echo htmlspecialchars($submission['assignment_title']); ?></h3>
                <p>Student: <?php echo htmlspecialchars($submission['student_name']); ?></p>
                <p>Submitted: <?php echo htmlspecialchars($submission['submitted_at']); ?></p>
                
                <div class="submission-content">
                    <h4>Submission Content:</h4>
                    <div class="content-box">
                        <?php echo nl2br(htmlspecialchars($submission['content'])); ?>
                    </div>
                </div>
                
                <?php if($submission['file_name']): ?>
                    <div class="submission-file">
                        <h4>Attached File:</h4>
                        <p><a href="uploads/<?php echo $submission['file_name']; ?>" target="_blank" class="file-link"><?php echo $submission['file_name']; ?></a></p>
                    </div>
                <?php endif; ?>
                
                <div class="grading-form">
                    <h4><?php echo isset($submission['grade']) ? 'Update Grade' : 'Assign Grade'; ?></h4>
                    
                    <form action="grade-submission.php?id=<?php echo $submission_id; ?>" method="post">
                        <div class="form-group">
                            <label for="grade">Grade (0-100)</label>
                            <input type="number" id="grade" name="grade" min="0" max="100" value="<?php echo isset($submission['grade']) ? $submission['grade'] : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="feedback">Feedback</label>
                            <textarea id="feedback" name="feedback" rows="5"><?php echo isset($submission['feedback']) ? $submission['feedback'] : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn"><?php echo isset($submission['grade']) ? 'Update Grade' : 'Submit Grade'; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        
        <!-- <?php include 'includes/footer.php'; ?> -->
    </div>
</body>
</html>
