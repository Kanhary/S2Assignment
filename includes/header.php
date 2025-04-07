<?php
// session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Collection System</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Arial", sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f4f6f9;
            transition: background-color 0.3s ease;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #1e293b;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            transition: transform 0.3s ease;
            box-shadow: 4px 0px 10px rgba(0, 0, 0, 0.2);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: bold;
            color: #ffffff;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .sidebar ul li a {
            color: #ffffff;
            text-decoration: none;
            flex-grow: 1;
            font-size: 16px;
        }

        .sidebar ul li:hover {
            background: #334155;
        }

        /* Dropdown Menu */
        .dropdown .dropdown-content {
            display: none;
            flex-direction: column;
            background: #334155;
            padding-left: 20px;
            transition: all 0.3s;
        }

        .dropdown.active .dropdown-content {
            display: flex;
        }

        .dropdown-content a {
            padding: 10px 15px;
            color: #e2e8f0;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }

        .dropdown-content a:hover {
            background: #475569;
        }

        .dropdown-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 30px;
            width: 100%;
            transition: margin-left 0.3s ease;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .main-content h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #1e293b;
        }

        /* Mobile Sidebar */
        .menu-toggle {
            display: none;
            position: absolute;
            top: 15px;
            left: 15px;
            background: #1e293b;
            color: white;
            padding: 10px 12px;
            border: none;
            cursor: pointer;
            font-size: 18px;
            z-index: 1000;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .menu-toggle:hover {
            background: #334155;
        }

        /* Overlay for Mobile */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-260px);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .menu-toggle {
                display: block;
            }

            .overlay.active {
                display: block;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar Toggle Button -->
<button class="menu-toggle" aria-label="Toggle Sidebar">☰</button>
<div class="overlay" aria-hidden="true"></div>

<!-- Sidebar -->
<div class="sidebar" role="navigation">
    <h2>Assignment System</h2>
    <ul>
        <li><a href="content.php" aria-label="Go to Home">Home</a></li>

        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if($_SESSION['user_role'] == 'teacher'): ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">Actions <span>▼</span></a>
                    <div class="dropdown-content">
                        <a href="create-assignment.php">Create Assignment</a>
                        <a href="view-submissions.php">View Submissions</a>
                        <a href="manage-classes.php">Manage Classes</a>
                    </div>
                </li>
            <?php else: ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">Actions <span>▼</span></a>
                    <div class="dropdown-content">
                        <a href="my-assignments.php">My Assignments</a>
                        <a href="my-submissions.php">My Submissions</a>
                    </div>
                </li>
            <?php endif; ?>

            <li><a href="profile.php" aria-label="Go to Profile">Profile</a></li>
            <li><a href="logout.php" aria-label="Logout">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" aria-label="Login">Login</a></li>
            <li><a href="register.php" aria-label="Register">Register</a></li>
        <?php endif; ?>
    </ul>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.querySelector('.sidebar');
        const toggleButton = document.querySelector('.menu-toggle');
        const overlay = document.querySelector('.overlay');
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

        // Toggle Sidebar
        toggleButton.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        // Close Sidebar When Clicking Outside
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        // Toggle Dropdown Menus
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function (event) {
                event.preventDefault();
                this.parentElement.classList.toggle('active');
                const dropdownContent = this.nextElementSibling;

                if (this.parentElement.classList.contains('active')) {
                    dropdownContent.style.display = 'flex';
                } else {
                    dropdownContent.style.display = 'none';
                }

                // Close other dropdowns
                dropdownToggles.forEach(otherToggle => {
                    if (otherToggle !== this) {
                        otherToggle.parentElement.classList.remove('active');
                        otherToggle.nextElementSibling.style.display = 'none';
                    }
                });
            });
        });
    });
</script>

</body>
</html>