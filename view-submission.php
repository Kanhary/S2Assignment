<?php
// Include the file where getSubmissionById is defined
require_once 'config/database.php';
require_once 'includes/functions.php'; // Ensure this path is correct for your project structure

// Ensure that the submission data is retrieved correctly from the database
$submission_id = $_GET['id'] ?? null; // Get the submission ID from URL parameter

if ($submission_id) {
    $submission = getSubmissionById($submission_id); // Call the function to fetch submission by ID
} else {
    echo "Submission ID is missing!";
    exit;
}

// Check if the submission exists
if (!$submission) {
    echo "Submission not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submission - Assignment Collection System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar inclusion -->
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="submission-main">
            <div class="back-link">
                <a href="view-submissions.php" class="btn btn-back">← Back to Submissions</a>
            </div>
            
            <section class="submission-details">
                <h2 class="submission-title"><?php echo htmlspecialchars($submission['assignment_title'] ?? 'No Title'); ?></h2>
                <p class="class-info"><strong>Class:</strong> <?php echo htmlspecialchars($submission['class_name'] ?? 'N/A'); ?></p>
                <p class="student-info"><strong>Student:</strong> <?php echo htmlspecialchars($submission['student_name'] ?? 'N/A'); ?></p>
                <p class="due-date"><strong>Due Date:</strong> <?php echo htmlspecialchars($submission['due_date'] ?? 'N/A'); ?></p>
                <p class="submitted-date"><strong>Submitted:</strong> <?php echo htmlspecialchars($submission['submitted_at'] ?? 'N/A'); ?></p>
                
                <div class="assignment-description">
                    <h4 class="section-title">Assignment Description:</h4>
                    <p><?php echo nl2br(htmlspecialchars($submission['assignment_description'] ?? 'No description available')); ?></p>
                </div>
                
                <div class="submission-content">
                    <h4 class="section-title">Submission Content:</h4>
                    <div class="content-box">
                        <?php echo nl2br(htmlspecialchars($submission['content'] ?? 'No content available')); ?>
                    </div>
                </div>
                
                <!-- Check if a file was uploaded -->
                <?php if (!empty($submission['file_name'])): ?>
                    <div class="submission-file">
                        <h4 class="section-title">Attached File:</h4>
                        <p><a href="uploads/<?php echo htmlspecialchars($submission['file_name']); ?>" target="_blank" class="file-link"><?php echo htmlspecialchars($submission['file_name']); ?></a></p>
                    </div>
                <?php endif; ?>
                
                <div class="submission-grade">
                    <h4 class="section-title">Grade:</h4>
                    <?php if (isset($submission['grade']) && !empty($submission['grade'])): ?>
                        <p class="grade"><?php echo htmlspecialchars($submission['grade']); ?>/100</p>
                        <div class="feedback">
                            <h4 class="feedback-title">Feedback:</h4>
                            <div class="feedback-box">
                                <?php echo nl2br(htmlspecialchars($submission['feedback'] ?? 'No feedback available')); ?>
                            </div>
                        </div>
                        <p class="graded-date"><strong>Graded on:</strong> <?php echo htmlspecialchars($submission['graded_at'] ?? 'N/A'); ?></p>
                    <?php else: ?>
                        <p class="not-graded">Not graded yet</p>
                    <?php endif; ?>
                </div>
                
                <!-- Actions based on the grade status -->
                <div class="submission-actions">
                    <?php if (isset($submission['grade']) && !empty($submission['grade'])): ?>
                        <a href="grade-submission.php?id=<?php echo $submission_id; ?>" class="btn btn-update">Update Grade</a>
                    <?php else: ?>
                        <a href="grade-submission.php?id=<?php echo $submission_id; ?>" class="btn btn-grade">Grade Submission</a>
                    <?php endif; ?>
                </div>
            </section>
        </main>
        
        <!-- Optional Footer -->
        <!-- <?php include 'includes/footer.php'; ?> -->
    </div>
</body>
</html>
