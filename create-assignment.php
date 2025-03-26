<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in and is a teacher
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'teacher') {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// Get classes taught by the teacher
$classes = getTeacherClasses($_SESSION['user_id']);

// Process form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $due_date = $_POST['due_date'] ?? '';
    $class_id = $_POST['class_id'] ?? '';
    
    if(empty($title) || empty($description) || empty($due_date) || empty($class_id)) {
        $error = "Please fill in all fields";
    } else {
        // Create the assignment
        if(createAssignment($title, $description, $due_date, $class_id, $_SESSION['user_id'])) {
            $success = "Assignment created successfully!";
        } else {
            $error = "Failed to create assignment. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assignment - Assignment Collection System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main>
            <div class="form-container">
                <h2>Create New Assignment</h2>
                
                <?php if(!empty($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($success)): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form action="create-assignment.php" method="post">
                    <div class="form-group">
                        <label for="title">Assignment Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="5" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="datetime-local" id="due_date" name="due_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="class_id">Class</label>
                        <select id="class_id" name="class_id" required>
                            <option value="">Select Class</option>
                            <?php foreach($classes as $class): ?>
                                <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn">Create Assignment</button>
                    </div>
                </form>
            </div>
        </main>
        
        <!-- <?php include 'includes/footer.php'; ?> -->
    </div>
</body>
</html>

