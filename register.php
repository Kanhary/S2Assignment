<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

// Process registration form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    if(empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = "Please fill in all fields";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetchColumn() > 0) {
            $error = "Email already exists";
        } else {
            // Register the user
            if(registerUser($name, $email, $password, $role)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Assignment Collection System</title>
    <style>
        /* General Container Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .container {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Main content area */
        main {
            width: 100%;
            max-width: 600px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-left: 680px;
        }

        /* Auth Form */
        .auth-form h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #2c3e50;
            text-align: center;
        }

        .auth-form .form-group {
            margin-bottom: 20px;
        }

        .auth-form label {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .auth-form input,
        .auth-form select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-top: 5px;
        }

        .auth-form button {
            width: 100%;
            padding: 14px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            margin-top: 20px;
        }

        .auth-form button:hover {
            background-color: #2980b9;
        }

        .auth-form .error-message,
        .auth-form .success-message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
        }

        .auth-form .error-message {
            background-color: #e74c3c;
            color: white;
        }

        .auth-form .success-message {
            background-color: #2ecc71;
            color: white;
        }

        .auth-form p {
            text-align: center;
            font-size: 14px;
            margin-top: 20px;
        }

        .auth-form a {
            color: #3498db;
            text-decoration: none;
        }

        .auth-form a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .auth-form {
                padding: 20px;
            }
            .main{
                margin: 0;
            }

            .auth-form h2 {
                font-size: 24px;
            }

            .auth-form button {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main>
            <div class="auth-form">
                <h2>University Registration</h2>
                
                <?php if(!empty($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($success)): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form action="register.php" method="post">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn">Register</button>
                    </div>
                </form>
                
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </main>
    </div>
</body>
</html>
