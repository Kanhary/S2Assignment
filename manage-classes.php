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

// Process class creation form
if(isset($_POST['create_class'])) {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if(empty($name)) {
        $error = "Class name is required";
    } else {
        // Create the class
        $stmt = $pdo->prepare("INSERT INTO classes (name, description, teacher_id) VALUES (?, ?, ?)");
        if($stmt->execute([$name, $description, $_SESSION['user_id']])) {
            $success = "Class created successfully!";
            // Refresh the classes list
            $classes = getTeacherClasses($_SESSION['user_id']);
        } else {
            $error = "Failed to create class. Please try again.";
        }
    }
}

// Process class deletion
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $class_id = $_GET['delete'];
    
    // Check if the class belongs to the teacher
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$class_id, $_SESSION['user_id']]);
    
    if($stmt->fetchColumn() > 0) {
        // Delete the class
        $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
        if($stmt->execute([$class_id])) {
            $success = "Class deleted successfully!";
            // Refresh the classes list
            $classes = getTeacherClasses($_SESSION['user_id']);
        } else {
            $error = "Failed to delete class. Please try again.";
        }
    } else {
        $error = "You don't have permission to delete this class.";
    }
}

// Get class details for editing
$edit_class = null;
if(isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $class_id = $_GET['edit'];
    
    // Check if the class belongs to the teacher
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$class_id, $_SESSION['user_id']]);
    $edit_class = $stmt->fetch();
    
    if(!$edit_class) {
        $error = "You don't have permission to edit this class.";
    }
}

// Process class update
if(isset($_POST['update_class']) && isset($_POST['class_id']) && is_numeric($_POST['class_id'])) {
    $class_id = $_POST['class_id'];
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if(empty($name)) {
        $error = "Class name is required";
    } else {
        // Check if the class belongs to the teacher
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE id = ? AND teacher_id = ?");
        $stmt->execute([$class_id, $_SESSION['user_id']]);
        
        if($stmt->fetchColumn() > 0) {
            // Update the class
            $stmt = $pdo->prepare("UPDATE classes SET name = ?, description = ? WHERE id = ?");
            if($stmt->execute([$name, $description, $class_id])) {
                $success = "Class updated successfully!";
                // Refresh the classes list
                $classes = getTeacherClasses($_SESSION['user_id']);
                // Clear edit mode
                $edit_class = null;
            } else {
                $error = "Failed to update class. Please try again.";
            }
        } else {
            $error = "You don't have permission to update this class.";
        }
    }
}

// Get students for a class
$class_students = [];
$available_students = [];
$current_class = null;

if(isset($_GET['manage']) && is_numeric($_GET['manage'])) {
    $class_id = $_GET['manage'];
    
    // Check if the class belongs to the teacher
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$class_id, $_SESSION['user_id']]);
    $current_class = $stmt->fetch();
    
    if($current_class) {
        // Get students enrolled in this class
        $stmt = $pdo->prepare("
            SELECT u.id, u.name, u.email
            FROM users u
            JOIN class_enrollments ce ON u.id = ce.student_id
            WHERE ce.class_id = ? AND u.role = 'student'
            ORDER BY u.name
        ");
        $stmt->execute([$class_id]);
        $class_students = $stmt->fetchAll();
        
        // Get students not enrolled in this class
        $stmt = $pdo->prepare("
            SELECT u.id, u.name, u.email
            FROM users u
            WHERE u.role = 'student' AND u.id NOT IN (
                SELECT student_id FROM class_enrollments WHERE class_id = ?
            )
            ORDER BY u.name
        ");
        $stmt->execute([$class_id]);
        $available_students = $stmt->fetchAll();
    } else {
        $error = "You don't have permission to manage this class.";
    }
}

// Process adding a student to class
if(isset($_POST['add_student']) && isset($_POST['class_id']) && isset($_POST['student_id'])) {
    $class_id = $_POST['class_id'];
    $student_id = $_POST['student_id'];
    
    // Check if the class belongs to the teacher
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$class_id, $_SESSION['user_id']]);
    
    if($stmt->fetchColumn() > 0) {
        // Check if the student is already enrolled
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM class_enrollments WHERE class_id = ? AND student_id = ?");
        $stmt->execute([$class_id, $student_id]);
        
        if($stmt->fetchColumn() == 0) {
            // Add the student to the class
            $stmt = $pdo->prepare("INSERT INTO class_enrollments (class_id, student_id) VALUES (?, ?)");
            if($stmt->execute([$class_id, $student_id])) {
                $success = "Student added to class successfully!";
                // Refresh the students lists
                header("Location: manage-classes.php?manage=$class_id");
                exit;
            } else {
                $error = "Failed to add student to class. Please try again.";
            }
        } else {
            $error = "Student is already enrolled in this class.";
        }
    } else {
        $error = "You don't have permission to manage this class.";
    }
}

// Process removing a student from class
if(isset($_GET['remove_student']) && isset($_GET['class_id']) && isset($_GET['student_id'])) {
    $class_id = $_GET['class_id'];
    $student_id = $_GET['student_id'];
    
    // Check if the class belongs to the teacher
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$class_id, $_SESSION['user_id']]);
    
    if($stmt->fetchColumn() > 0) {
        // Remove the student from the class
        $stmt = $pdo->prepare("DELETE FROM class_enrollments WHERE class_id = ? AND student_id = ?");
        if($stmt->execute([$class_id, $student_id])) {
            $success = "Student removed from class successfully!";
            // Refresh the students lists
            header("Location: manage-classes.php?manage=$class_id");
            exit;
        } else {
            $error = "Failed to remove student from class. Please try again.";
        }
    } else {
        $error = "You don't have permission to manage this class.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes - Assignment Collection System</title>
    <style>
        
    </style>
</head>
<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main>
            <h2>Manage Classes</h2>
            
            <?php if(!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if($current_class): ?>
                <!-- Manage students in a class -->
                <div class="manage-students">
                    <h3>Manage Students in <?php echo htmlspecialchars($current_class['name']); ?></h3>
                    
                    <div class="back-link">
                        <a href="manage-classes.php" class="btn-small">Back to Classes</a>
                    </div>
                    
                    <div class="class-students">
                        <h4>Enrolled Students</h4>
                        <?php if(count($class_students) > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($class_students as $student): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                                            <td>
                                                <a href="manage-classes.php?manage=<?php echo $current_class['id']; ?>&remove_student=1&class_id=<?php echo $current_class['id']; ?>&student_id=<?php echo $student['id']; ?>" class="btn-small" onclick="return confirm('Are you sure you want to remove this student from the class?')">Remove</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No students enrolled in this class yet.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="add-student">
                        <h4>Add Student to Class</h4>
                        <?php if(count($available_students) > 0): ?>
                            <form action="manage-classes.php?manage=<?php echo $current_class['id']; ?>" method="post">
                                <input type="hidden" name="class_id" value="<?php echo $current_class['id']; ?>">
                                
                                <div class="form-group">
                                    <label for="student_id">Select Student</label>
                                    <select id="student_id" name="student_id" required>
                                        <option value="">Select Student</option>
                                        <?php foreach($available_students as $student): ?>
                                            <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['name']); ?> (<?php echo htmlspecialchars($student['email']); ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" name="add_student" class="btn">Add Student</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <p>No more students available to add to this class.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif($edit_class): ?>
                <!-- Edit class form -->
                <div class="edit-class">
                    <h3>Edit Class</h3>
                    
                    <form action="manage-classes.php" method="post">
                        <input type="hidden" name="class_id" value="<?php echo $edit_class['id']; ?>">
                        
                        <div class="form-group">
                            <label for="name">Class Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($edit_class['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($edit_class['description']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="update_class" class="btn">Update Class</button>
                            <a href="manage-classes.php" class="btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Create class form -->
                <div class="create-class">
                    <h3>Create New Class</h3>
                    
                    <form action="manage-classes.php" method="post">
                        <div class="form-group">
                            <label for="name">Class Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="create_class" class="btn">Create Class</button>
                        </div>
                    </form>
                </div>
                
                <!-- List of classes -->
                <div class="classes-list">
                    <h3>Your Classes</h3>
                    
                    <?php if(count($classes) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Class Name</th>
                                    <th>Description</th>
                                    <th>Students</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($classes as $class): ?>
                                    <?php
                                        // Get number of students in this class
                                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM class_enrollments WHERE class_id = ?");
                                        $stmt->execute([$class['id']]);
                                        $student_count = $stmt->fetchColumn();
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($class['name']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($class['description'], 0, 100)) . (strlen($class['description']) > 100 ? '...' : ''); ?></td>
                                        <td><?php echo $student_count; ?></td>
                                        <td>
                                            <a href="manage-classes.php?manage=<?php echo $class['id']; ?>" class="btn-small">Manage Students</a>
                                            <a href="manage-classes.php?edit=<?php echo $class['id']; ?>" class="btn-small">Edit</a>
                                            <a href="manage-classes.php?delete=<?php echo $class['id']; ?>" class="btn-small" onclick="return confirm('Are you sure you want to delete this class? This will also delete all assignments and submissions associated with this class.')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>You haven't created any classes yet.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
        
        <!-- <?php include 'includes/footer.php'; ?> -->
    </div>
</body>
</html>

