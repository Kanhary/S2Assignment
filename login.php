<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        if (loginUser ($email, $password)) {
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
        body {
            font-family: 'Arial', sans-serif;
            /* background-color: #f0f4f8; */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
           
        }

        /* Sidebar Styles */
        /* <?php include('sidebar.css'); ?> */

        /* Main Content */
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 20px;
        }

        /* Login Form */
        .auth-form {
            width: 100%;
            max-width: 450px;
            /* background: #ffffff; */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .auth-form h2 {
            margin-bottom: 30px;
            color: #333;
            font-size: 28px;
            font-weight: 600;
        }

        .auth-form .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .auth-form label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        .auth-form input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .auth-form input:focus {
            border-color: #3498db;
            outline: none;
        }

        .auth-form .btn {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
            font-weight: bold;
        }

        .auth-form .btn:hover {
            background: #2980b9;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
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

            <?php if (!empty($error)): ?>
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

            <p class="footer">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </main>
</body>
</html>