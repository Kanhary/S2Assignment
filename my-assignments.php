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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
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
                    <p>Filter by status:</p>
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
    </div>
</body>
</html>

