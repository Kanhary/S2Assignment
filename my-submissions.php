<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in and is a student
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: login.php");
    exit;
}

// Get all submissions for the student
$stmt = $pdo->prepare("
    SELECT s.*, a.title as assignment_title, a.due_date, c.name as class_name
    FROM submissions s
    JOIN assignments a ON s.assignment_id = a.id
    JOIN classes c ON a.class_id = c.id
    WHERE s.student_id = ?
    ORDER BY s.submitted_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$submissions = $stmt->fetchAll();

// Get submission details if viewing a specific submission
$submission_details = null;
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $submission_id = $_GET['id'];
    
    // Check if the submission belongs to the student
    $stmt = $pdo->prepare("
        SELECT s.*, a.title as assignment_title, a.description as assignment_description, 
               a.due_date, c.name as class_name, u.name as teacher_name
        FROM submissions s
        JOIN assignments a ON s.assignment_id = a.id
        JOIN classes c ON a.class_id = c.id
        JOIN users u ON a.teacher_id = u.id
        WHERE s.id = ? AND s.student_id = ?
    ");
    $stmt->execute([$submission_id, $_SESSION['user_id']]);
    $submission_details = $stmt->fetch();
    
    if(!$submission_details) {
        header("Location: my-submissions.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Submissions - Assignment Collection System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main>
            <?php if($submission_details): ?>
                <!-- View specific submission details -->
                <div class="submission-details">
                    <div class="back-link">
                        <a href="my-submissions.php" class="btn-small">Back to All Submissions</a>
                    </div>
                    
                    <h2>Submission Details</h2>
                    
                    <div class="submission-info">
                        <h3><?php echo htmlspecialchars($submission_details['assignment_title']); ?></h3>
                        <p class="class-info">Class: <?php echo htmlspecialchars($submission_details['class_name']); ?></p>
                        <p class="teacher-info">Teacher: <?php echo htmlspecialchars($submission_details['teacher_name']); ?></p>
                        <p class="due-date">Due Date: <?php echo htmlspecialchars($submission_details['due_date']); ?></p>
                        <p class="submitted-date">Submitted: <?php echo htmlspecialchars($submission_details['submitted_at']); ?></p>
                        
                        <div class="assignment-description">
                            <h4>Assignment Description:</h4>
                            <p><?php echo nl2br(htmlspecialchars($submission_details['assignment_description'])); ?></p>
                        </div>
                        
                        <div class="submission-content">
                            <h4>Your Submission:</h4>
                            <div class="content-box">
                                <?php echo nl2br(htmlspecialchars($submission_details['content'])); ?>
                            </div>
                        </div>
                        
                        <?php if($submission_details['file_name']): ?>
                            <div class="submission-file">
                                <h4>Attached File:</h4>
                                <p><a href="uploads/<?php echo $submission_details['file_name']; ?>" target="_blank" class="file-link"><?php echo $submission_details['file_name']; ?></a></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="submission-grade">
                            <h4>Grade:</h4>
                            <?php if(isset($submission_details['grade'])): ?>
                                <p class="grade"><?php echo $submission_details['grade']; ?>/100</p>
                                <div class="feedback">
                                    <h4>Teacher Feedback:</h4>
                                    <div class="feedback-box">
                                        <?php echo nl2br(htmlspecialchars($submission_details['feedback'])); ?>
                                    </div>
                                </div>
                                <p class="graded-date">Graded on: <?php echo htmlspecialchars($submission_details['graded_at']); ?></p>
                            <?php else: ?>
                                <p class="not-graded">Not graded yet</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- List all submissions -->
                <h2>My Submissions</h2>
                
                <?php if(count($submissions) > 0): ?>
                    <div class="submissions-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Assignment</th>
                                    <th>Class</th>
                                    <th>Submitted Date</th>
                                    <th>Status</th>
                                    <th>Grade</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($submissions as $submission): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($submission['assignment_title']); ?></td>
                                        <td><?php echo htmlspecialchars($submission['class_name']); ?></td>
                                        <td><?php echo htmlspecialchars($submission['submitted_at']); ?></td>
                                        <td>
                                            <?php if(isset($submission['grade'])): ?>
                                                <span class="status-graded">Graded</span>
                                            <?php else: ?>
                                                <span class="status-pending">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isset($submission['grade'])): ?>
                                                <?php echo $submission['grade']; ?>/100
                                            <?php else: ?>
                                                <span class="not-graded">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="my-submissions.php?id=<?php echo $submission['id']; ?>" class="btn-small">View Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>You haven't submitted any assignments yet.</p>
                <?php endif; ?>
            <?php endif; ?>
        </main>
        
        <!-- <?php include 'includes/footer.php'; ?> -->
    </div>
</body>
</html>

