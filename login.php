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

// Process login form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if(empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        if(loginUser($email, $password)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Assignment Collection System</title>
    <style>
        

        /* Sidebar Styles */
        <?php include('sidebar.css'); ?>

        /* Main Content */
        .main-content {
            margin-left: 250px;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            
        }

        /* Login Form */
        .auth-form {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .auth-form h2 {
            margin-bottom: 20px;
        }

        .auth-form .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .auth-form label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .auth-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .auth-form .btn {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .auth-form .btn:hover {
            background: #0056b3;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
                padding: 10px;
            }
            .auth-form {
                width: 90%;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 10px;
                text-align: center;
            }

            .sidebar ul {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }

            .sidebar ul li {
                padding: 5px;
            }

            .main-content {
                margin-left: 0;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'includes/header.php';?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="auth-form">
            <h2>Login</h2>

            <?php if(!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Login</button>
                </div>
            </form>

            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </main>
</body>
</html>
