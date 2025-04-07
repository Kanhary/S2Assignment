<?php
// Function to get assignments created by a teacher
function getTeacherAssignments($teacherId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM assignments WHERE teacher_id = ? ORDER BY created_at DESC");
    $stmt->execute([$teacherId]);
    
    return $stmt->fetchAll();
}

// Function to get assignments for a student
function getStudentAssignments($studentId) {
    global $pdo;
    
    // Get all assignments from classes the student is enrolled in
    $stmt = $pdo->prepare("
        SELECT a.* FROM assignments a
        JOIN class_enrollments ce ON a.class_id = ce.class_id
        WHERE ce.student_id = ?
        ORDER BY a.due_date ASC
    ");
    $stmt->execute([$studentId]);
    
    return $stmt->fetchAll();
}

// Check if a student has submitted an assignment
function isSubmitted($studentId, $assignmentId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM submissions WHERE student_id = ? AND assignment_id = ?");
    $stmt->execute([$studentId, $assignmentId]);
    
    return $stmt->fetchColumn() > 0;
}

// Get submission details
function getSubmission($studentId, $assignmentId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM submissions WHERE student_id = ? AND assignment_id = ?");
    $stmt->execute([$studentId, $assignmentId]);
    
    return $stmt->fetch();
}

// Get assignment details
function getAssignment($assignmentId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM assignments WHERE id = ?");
    $stmt->execute([$assignmentId]);
    
    return $stmt->fetch();
}

// Get all submissions for an assignment
function getAssignmentSubmissions($assignmentId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT s.*, u.name as student_name
        FROM submissions s
        JOIN users u ON s.student_id = u.id
        WHERE s.assignment_id = ?
        ORDER BY s.submitted_at DESC
    ");
    $stmt->execute([$assignmentId]);
    
    return $stmt->fetchAll();
}

// Register a new user
function registerUser($name, $email, $password, $role) {
    global $pdo;
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$name, $email, $hashedPassword, $role]);
}

// Login a user
function loginUser($email, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['password'])) {
        // Password is correct, start a new session
        session_start();
        
        // Store user data in session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        return true;
    }
    
    return false;
}

// Create a new assignment
function createAssignment($title, $description, $dueDate, $classId, $teacherId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO assignments (title, description, due_date, class_id, teacher_id, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    return $stmt->execute([$title, $description, $dueDate, $classId, $teacherId]);
}

// Submit an assignment
function submitAssignment($studentId, $assignmentId, $content, $fileName = null) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO submissions (student_id, assignment_id, content, file_name, submitted_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    return $stmt->execute([$studentId, $assignmentId, $content, $fileName]);
}

// Grade a submission
function gradeSubmission($submissionId, $grade, $feedback) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        UPDATE submissions
        SET grade = ?, feedback = ?, graded_at = NOW()
        WHERE id = ?
    ");
    
    return $stmt->execute([$grade, $feedback, $submissionId]);
}

// Get classes taught by a teacher
function getTeacherClasses($teacherId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE teacher_id = ?");
    $stmt->execute([$teacherId]);
    
    return $stmt->fetchAll();
}

// Get classes a student is enrolled in
function getStudentClasses($studentId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT c.* FROM classes c
        JOIN class_enrollments ce ON c.id = ce.class_id
        WHERE ce.student_id = ?
    ");
    $stmt->execute([$studentId]);
    
    return $stmt->fetchAll();
}

function getAssignmentById($id) {
    global $pdo; // Ensure you are using the PDO connection

    $stmt = $pdo->prepare("SELECT * FROM assignments WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(); // Fetch assignment data as an associative array
}


function getSubmittedFile($student_id, $assignment_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT file_name FROM submissions WHERE student_id = ? AND assignment_id = ?");
    $stmt->execute([$student_id, $assignment_id]);
    return $stmt->fetch();
}

function updateAssignment($id, $title, $description, $due_date) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE assignments SET title = ?, description = ?, due_date = ? WHERE id = ?");
    return $stmt->execute([$title, $description, $due_date, $id]);
}



// Database connection (example using PDO)
function getDbConnection() {
    $host = 'localhost';
    $dbname = 'assignment_system'; // Change this to your database name
    $username = 'root'; // Your database username
    $password = ''; // Your database password (empty for WAMP default)
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Function to get submission by ID
function getSubmissionById($submission_id) {
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM submissions WHERE id = :submission_id");
    $stmt->bindParam(':submission_id', $submission_id, PDO::PARAM_INT);
    
    $stmt->execute();
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $submission;
}
