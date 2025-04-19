<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$submission_id = $_GET['id'] ?? null;

if ($submission_id) {
    $submission = getSubmissionById($submission_id);
} else {
    echo "Submission ID is missing!";
    exit;
}

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
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
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
            margin-bottom: 15px;
        }
        .btn {
            background-color: #3c8dbc;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 14px;
            display: inline-block;
            transition: 0.3s ease;
        }
        .btn:hover {
            background-color: #367fa9;
        }
        h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .submission-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 20px rgba(31, 45, 84, 0.26);
        border-left:5px solid  rgb(30, 42, 76);
        }
        .submission-details h4 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        .submission-details p {
            font-size: 16px;
            color: #666;
            margin: 10px 0;
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
        .file-link {
            color: #3c8dbc;
            text-decoration: none;
        }
        .file-link:hover {
            text-decoration: underline;
        }
        .submission-grade {
            margin-top: 20px;
        }
        .grade {
            font-size: 18px;
            font-weight: bold;
            color: #3c763d;
        }
        .not-graded {
            font-size: 16px;
            color: #d9534f;
        }
        .feedback-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #3c8dbc;
            font-size: 16px;
            color: #555;
            margin-top: 10px;
        }
        .submission-actions {
            margin-top: 20px;
        }
        .btn-update {
            background:rgb(9, 29, 55);  
        }
        .btn-grade {
            background-color: #00a65a;
        }
    </style>
</head>
<body>

        <?php include 'includes/sidebar.php'; ?>

        <main>
                <!-- <div class="back-link">
                    <a href="view-submissions.php" class="btn">Back to Submissions</a>
                </div> -->

            <section class="submission-details">
                <h2><?php echo htmlspecialchars($submission['assignment_title'] ?? 'No Title'); ?></h2>
                <p><strong>Class:</strong> <?php echo htmlspecialchars($submission['class_name'] ?? 'N/A'); ?></p>
                <p><strong>Student:</strong> <?php echo htmlspecialchars($submission['student_name'] ?? 'N/A'); ?></p>
                <p><strong>Due Date:</strong> <?php echo htmlspecialchars($submission['due_date'] ?? 'N/A'); ?></p>
                <p><strong>Submitted:</strong> <?php echo htmlspecialchars($submission['submitted_at'] ?? 'N/A'); ?></p>

                <div class="assignment-description">
                    <h4>Assignment Description:</h4>
                    <p><?php echo nl2br(htmlspecialchars($submission['assignment_description'] ?? 'No description available')); ?></p>
                </div>

                <div class="submission-content">
                    <h4>Submission Content:</h4>
                    <div class="content-box">
                        <?php echo nl2br(htmlspecialchars($submission['content'] ?? 'No content available')); ?>
                    </div>
                </div>

                <?php if (!empty($submission['file_name'])): ?>
                    <div class="submission-file">
                        <h4>Attached File:</h4>
                        <p><a href="uploads/<?php echo htmlspecialchars($submission['file_name']); ?>" target="_blank" class="file-link"><?php echo htmlspecialchars($submission['file_name']); ?></a></p>
                    </div>
                <?php endif; ?>

                <div class="submission-grade">
                    <h4>Grade:</h4>
                    <?php if (!empty($submission['grade'])): ?>
                        <p class="grade"><?php echo htmlspecialchars($submission['grade']); ?>/100</p>
                        <div class="feedback">
                            <h4>Feedback:</h4>
                            <div class="feedback-box">
                                <?php echo nl2br(htmlspecialchars($submission['feedback'] ?? 'No feedback available')); ?>
                            </div>
                        </div>
                        <p><strong>Graded on:</strong> <?php echo htmlspecialchars($submission['graded_at'] ?? 'N/A'); ?></p>
                    <?php else: ?>
                        <p class="not-graded">Not graded yet</p>
                    <?php endif; ?>
                </div>

                <div class="submission-actions">
                    <?php if (!empty($submission['grade'])): ?>
                        <a href="grade-submission.php?id=<?php echo $submission_id; ?>" class="btn btn-update">Update Grade</a>
                    <?php else: ?>
                        <a href="grade-submission.php?id=<?php echo $submission_id; ?>" class="btn btn-grade">Grade Submission</a>
                    <?php endif; ?>
                </div>
            </section>
        </main>

</body>
</html>
