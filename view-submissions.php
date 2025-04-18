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
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
     <style>
        /* Main container setup */
.container {
    width: 100%;
    display: flex;
    min-height: 100vh;
    font-family: 'Roboto', sans-serif;
    color: #333;
}

/* Main content area */
.main-content {
    flex: 1;
    padding: 40px;
    background-color: #f4f7fb;
    margin-left: 260px; /* Adjust based on sidebar width */
    box-sizing: border-box;
}

/* Page header */
.page-header {
    margin-bottom: 30px;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
}

/* Assignment List Section */
.assignments-list {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

/* Individual assignment card */
.assignment-card {
    background-color: #ffffff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.assignment-card:hover {
    transform: translateY(-5px);
}

.assignment-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.assignment-title {
    font-size: 22px;
    font-weight: 600;
    color: #34495e;
}

.due-date {
    font-size: 16px;
    color: #7f8c8d;
}

.submission-count {
    font-size: 18px;
    color: #3498db;
    margin-bottom: 20px;
}

/* Table for submissions */
.submissions-table-container {
    overflow-x: auto;
}

.submissions-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.submissions-table th,
.submissions-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.submissions-table th {
    background-color: #ecf0f1;
    font-weight: 600;
    color: #34495e;
}

.submissions-table td {
    font-size: 16px;
}

.submissions-table td a {
    text-decoration: none;
    color: #3498db;
    background-color: #ecf0f1;
    padding: 8px 16px;
    border-radius: 5px;
    font-size: 14px;
    transition: background-color 0.2s ease;
}

.submissions-table td a:hover {
    background-color: #3498db;
    color: #fff;
}

/* Statuses */
.graded {
    color: #2ecc71;
    font-weight: 600;
}

.not-graded {
    color: #e74c3c;
    font-weight: 600;
}

/* Empty states */
.no-submissions,
.no-assignments {
    font-size: 18px;
    color: #7f8c8d;
    font-style: italic;
}

/* Small buttons */
.btn-small {
    padding: 8px 15px;
    font-size: 14px;
    border-radius: 4px;
    color: #fff;
    background-color: #3498db;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.btn-small:hover {
    background-color: #2980b9;
}

     </style>
</head>
<body>

        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="page-header">
                <h2 class="page-title">View Submissions</h2>
            </header>
            
            <?php if(count($assignments) > 0): ?>
                <section class="assignments-list">
                    <?php foreach($assignments as $assignment): ?>
                        <?php 
                            $submissions = getAssignmentSubmissions($assignment['id']);
                            $submissionCount = count($submissions);
                        ?>
                        <div class="assignment-card">
                            <div class="assignment-header">
                                <h3 class="assignment-title"><?php echo htmlspecialchars($assignment['title']); ?></h3>
                                <span class="due-date"><?php echo htmlspecialchars($assignment['due_date']); ?></span>
                            </div>
                            <p class="submission-count">Submissions: <?php echo $submissionCount; ?></p>
                            
                            <?php if($submissionCount > 0): ?>
                                <div class="submissions-table-container">
                                    <table class="submissions-table">
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
                                                            <span class="graded"><?php echo $submission['grade']; ?></span>
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
                                <p class="no-submissions">No submissions yet.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php else: ?>
                <p class="no-assignments">No assignments created yet.</p>
            <?php endif; ?>
        </main>
        

</body>
</html>
