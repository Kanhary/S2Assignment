<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in and is a student
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: login.php");
    exit;
}

// Get all assignments for the student
$assignments = getStudentAssignments($_SESSION['user_id']);

// Get assignment details if viewing a specific assignment
$assignment_details = null;
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $assignment_id = $_GET['id'];
    
    // Check if the assignment is available to the student
    $found = false;
    foreach($assignments as $assignment) {
        if($assignment['id'] == $assignment_id) {
            $found = true;
            break;
        }
    }
    
    if($found) {
        // Get detailed assignment information
        $stmt = $pdo->prepare("
            SELECT a.*, c.name as class_name, u.name as teacher_name
            FROM assignments a
            JOIN classes c ON a.class_id = c.id
            JOIN users u ON a.teacher_id = u.id
            WHERE a.id = ?
        ");
        $stmt->execute([$assignment_id]);
        $assignment_details = $stmt->fetch();
        
        // Check if the student has already submitted this assignment
        $is_submitted = isSubmitted($_SESSION['user_id'], $assignment_id);
        
        if($is_submitted) {
            // Get submission details
            $submission = getSubmission($_SESSION['user_id'], $assignment_id);
        }
    } else {
        header("Location: my-assignments.php");
        exit;
    }
}

// Filter assignments by status if requested
$filter = $_GET['filter'] ?? 'all';
$filtered_assignments = [];

if($filter == 'pending') {
    foreach($assignments as $assignment) {
        if(!isSubmitted($_SESSION['user_id'], $assignment['id'])) {
            $filtered_assignments[] = $assignment;
        }
    }
} elseif($filter == 'submitted') {
    foreach($assignments as $assignment) {
        if(isSubmitted($_SESSION['user_id'], $assignment['id'])) {
            $filtered_assignments[] = $assignment;
        }
    }
} else {
    $filtered_assignments = $assignments;
}

// Sort assignments by due date (closest first)
usort($filtered_assignments, function($a, $b) {
    return strtotime($a['due_date']) - strtotime($b['due_date']);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Assignments - Assignment Collection System</title>
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
    <style>
        /* Assignment Page Enhancements */
main {
    margin-left: 300px;
    width: calc(100% - 260px);
    min-height: 100vh;
    background-color: #f5f7fa;
    padding: 0px 10px;
    /* padding: 40px; */
    font-family: 'Segoe UI', sans-serif;
    padding-top: 15px;
}

h2 {
    font-size: 28px;
    color: #2c3e50;
    margin-bottom: 20px;
}

.assignment-card, .assignment-details {
    background: #ffffff;
    border-radius: 10px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: transform 0.2s ease;
    box-shadow: 0 4px 20px rgba(31, 45, 84, 0.26);
    border-left:5px solid  rgb(30, 42, 76);
}



.assignment-card:hover {
    transform: scale(1.01);
}

.assignment-card{
    box-shadow: 0 4px 20px rgba(31, 45, 84, 0.26);
    border-left:5px solid  rgb(30, 42, 76);
    max-width: 400px
}

.assignment-card.overdue {
    border-left: 5px solid #e74c3c;
}

.assignment-info h3 {
    font-size: 22px;
    margin-bottom: 10px;
    color: #34495e;
}
.assignments-grid{
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}
.assignment-info p,
.assignment-card p {
    margin: 6px 0;
    color: #666;
    font-size: 15px;
}

.due-date span,
.overdue-label,
.due-soon-label {
    font-weight: bold;
    margin-left: 10px;
}

.overdue-label {
    color: #e74c3c;
}
.due-soon-label {
    color: #f39c12;
}
.status-submitted,
.status-graded {
    color: #2ecc71;
}
.status-not-submitted,
.status-pending {
    color: #e67e22;
}
.status-graded {
    font-weight: bold;
}

.assignment-actions {
    margin-top: 15px;
}
.assignment-actions .btn,
.assignment-actions .btn-small {
    padding: 8px 14px;
    margin-right: 10px;
    background:rgb(9, 29, 55);
    border: none;
    border-radius: 5px;
    color: white;
    font-size: 14px;
    text-decoration: none;
    transition: background 0.3s ease;
}
.assignment-actions .btn:hover,
.assignment-actions .btn-small:hover {
    background:rgba(9, 29, 55, 0.7);
}

.filter-controls {
    margin-bottom: 25px;
}
.filter-controls p {
    font-weight: bold;
    margin-bottom: 8px;
}
.filter-link {
    text-decoration: none;
    margin-right: 12px;
    padding: 6px 12px;
    background: #ecf0f1;
    border-radius: 4px;
    color: #2c3e50;
}
.filter-link.active {
    background:rgb(9, 29, 55);
    color: white;
}

.description-box, .feedback-box {
    background-color: #f4f6f8;
    border-radius: 6px;
    padding: 10px;
    white-space: pre-line;
    font-size: 15px;
}

.no-assignments {
    text-align: center;
    font-size: 16px;
    color: #888;
    margin-top: 40px;
}

.back-link a {
    text-decoration: none;
    background: #7f8c8d;
    color: white;
    padding: 6px 12px;
    border-radius: 5px;
    font-size: 13px;
    margin-bottom: 15px;
    display: inline-block;
}
.back-link a:hover {
    background: #34495e;
}

    </style>
</head>
<body>
    <!-- <div class="container"> -->
        <?php include 'includes/sidebar.php'; ?>
        
        <main>
            <?php if($assignment_details): ?>
                <!-- View specific assignment details -->
                <div class="assignment-details">
                    <div class="back-link">
                        <a href="my-assignments.php" class="btn-small">Back to All Assignments</a>
                    </div>
                    
                    <h2>Assignment Details</h2>
                    
                    <div class="assignment-info">
                        <h3><?php echo htmlspecialchars($assignment_details['title']); ?></h3>
                        <p class="class-info">Class: <?php echo htmlspecialchars($assignment_details['class_name']); ?></p>
                        <p class="teacher-info">Teacher: <?php echo htmlspecialchars($assignment_details['teacher_name']); ?></p>
                        
                        <?php 
                            $due_date = strtotime($assignment_details['due_date']);
                            $now = time();
                            $is_overdue = $due_date < $now;
                            $days_left = round(($due_date - $now) / (60 * 60 * 24));
                        ?>
                        
                        <p class="due-date <?php echo $is_overdue ? 'overdue' : ''; ?>">
                            Due: <?php echo htmlspecialchars($assignment_details['due_date']); ?>
                            <?php if($is_overdue): ?>
                                <span class="overdue-label">(Overdue)</span>
                            <?php elseif($days_left <= 3): ?>
                                <span class="due-soon-label">(Due in <?php echo $days_left; ?> day<?php echo $days_left != 1 ? 's' : ''; ?>)</span>
                            <?php endif; ?>
                        </p>
                        
                        <div class="assignment-description">
                            <h4>Description:</h4>
                            <div class="description-box">
                                <?php echo nl2br(htmlspecialchars($assignment_details['description'])); ?>
                            </div>
                        </div>
                        
                        <div class="assignment-status">
                            <h4>Status:</h4>
                            <?php if(isset($is_submitted) && $is_submitted): ?>
                                <p class="status-submitted">Submitted on <?php echo htmlspecialchars($submission['submitted_at']); ?></p>
                                
                                <?php if(isset($submission['grade'])): ?>
                                    <p class="status-graded">Graded: <?php echo $submission['grade']; ?>/100</p>
                                    <div class="feedback">
                                        <h4>Teacher Feedback:</h4>
                                        <div class="feedback-box">
                                            <?php echo nl2br(htmlspecialchars($submission['feedback'])); ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p class="status-pending">Waiting for grade</p>
                                <?php endif; ?>
                                
                                <div class="assignment-actions">
                                    <a href="my-submissions.php?id=<?php echo $submission['id']; ?>" class="btn">View My Submission</a>
                                </div>
                            <?php else: ?>
                                <p class="status-not-submitted">Not submitted yet</p>
                                
                                <div class="assignment-actions">
                                    <a href="submit-assignment.php?id=<?php echo $assignment_details['id']; ?>" class="btn">Submit Assignment</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- List all assignments -->
                <h2>My Assignments</h2>
                
                <div class="filter-controls">
                    <!-- <p>Filter by status:</p> -->
                    <a href="my-assignments.php?filter=all" class="filter-link <?php echo $filter == 'all' ? 'active' : ''; ?>">All</a>
                    <a href="my-assignments.php?filter=pending" class="filter-link <?php echo $filter == 'pending' ? 'active' : ''; ?>">Pending</a>
                    <a href="my-assignments.php?filter=submitted" class="filter-link <?php echo $filter == 'submitted' ? 'active' : ''; ?>">Submitted</a>
                </div>
                
                <?php if(count($filtered_assignments) > 0): ?>
                    <div class="assignments-grid">
                        <?php foreach($filtered_assignments as $assignment): ?>
                            <?php 
                                $is_submitted = isSubmitted($_SESSION['user_id'], $assignment['id']);
                                $due_date = strtotime($assignment['due_date']);
                                $now = time();
                                $is_overdue = $due_date < $now;
                                $days_left = round(($due_date - $now) / (60 * 60 * 24));
                                
                                // Get class name
                                $stmt = $pdo->prepare("SELECT name FROM classes WHERE id = ?");
                                $stmt->execute([$assignment['class_id']]);
                                $class_name = $stmt->fetchColumn();
                            ?>
                            <div class="assignment-card <?php echo $is_submitted ? 'submitted' : ($is_overdue ? 'overdue' : ''); ?>">
                                <h3><?php echo htmlspecialchars($assignment['title']); ?></h3>
                                <p class="class-name">Class: <?php echo htmlspecialchars($class_name); ?></p>
                                <p class="due-date <?php echo $is_overdue ? 'overdue' : ''; ?>">
                                    Due: <?php echo htmlspecialchars($assignment['due_date']); ?>
                                    <?php if($is_overdue && !$is_submitted): ?>
                                        <span class="overdue-label">(Overdue)</span>
                                    <?php elseif($days_left <= 3 && !$is_submitted): ?>
                                        <span class="due-soon-label">(Due in <?php echo $days_left; ?> day<?php echo $days_left != 1 ? 's' : ''; ?>)</span>
                                    <?php endif; ?>
                                </p>
                                <p class="assignment-status">
                                    Status: 
                                    <?php if($is_submitted): ?>
                                        <?php 
                                            $submission = getSubmission($_SESSION['user_id'], $assignment['id']);
                                            if(isset($submission['grade'])):
                                        ?>
                                            <span class="status-graded">Graded (<?php echo $submission['grade']; ?>/100)</span>
                                        <?php else: ?>
                                            <span class="status-submitted">Submitted</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="status-not-submitted">Not Submitted</span>
                                    <?php endif; ?>
                                </p>
                                <div class="assignment-actions">
                                    <a href="my-assignments.php?id=<?php echo $assignment['id']; ?>" class="btn-small">View Details</a>
                                    <?php if(!$is_submitted): ?>
                                        <a href="submit-assignment.php?id=<?php echo $assignment['id']; ?>" class="btn-small">Submit</a>
                                    <?php else: ?>
                                        <a href="my-submissions.php?id=<?php echo $submission['id']; ?>" class="btn-small">View Submission</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-assignments">No assignments found with the selected filter.</p>
                <?php endif; ?>
            <?php endif; ?>
        </main>
        
        <!-- <?php include 'includes/footer.php'; ?> -->
    <!-- </div> -->
</body>
</html>

