<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in and is a teacher
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'teacher') {
    header("Location: login.php");
    exit;
}

// Get assignments created by the teacher
$assignments = getTeacherAssignments($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions - Assignment Collection System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main>
            <h2>View Submissions</h2>
            
            <?php if(count($assignments) > 0): ?>
                <div class="assignments-list">
                    <?php foreach($assignments as $assignment): ?>
                        <?php 
                            $submissions = getAssignmentSubmissions($assignment['id']);
                            $submissionCount = count($submissions);
                        ?>
                        <div class="assignment-card">
                            <h3><?php echo htmlspecialchars($assignment['title']); ?></h3>
                            <p class="due-date">Due: <?php echo htmlspecialchars($assignment['due_date']); ?></p>
                            <p class="submission-count">Submissions: <?php echo $submissionCount; ?></p>
                            
                            <?php if($submissionCount > 0): ?>
                                <div class="submissions-list">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Submitted</th>
                                                <th>Grade</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($submissions as $submission): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($submission['student_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($submission['submitted_at']); ?></td>
                                                    <td>
                                                        <?php if(isset($submission['grade'])): ?>
                                                            <?php echo $submission['grade']; ?>
                                                        <?php else: ?>
                                                            <span class="not-graded">Not Graded</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="view-submission.php?id=<?php echo $submission['id']; ?>" class="btn-small">View</a>
                                                        <a href="grade-submission.php?id=<?php echo $submission['id']; ?>" class="btn-small">Grade</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No submissions yet.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No assignments created yet.</p>
            <?php endif; ?>
        </main>
        
        <!-- <?php include 'includes/footer.php'; ?> -->
    </div>
</body>
</html>

