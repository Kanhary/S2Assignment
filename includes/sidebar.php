<!-- <?php
session_start();
?> -->

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
            width: 280px;
            height: 100vh;
            background: #1c2833;
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
            margin-bottom: 25px;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 14px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            transition: background 0.3s ease;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            flex-grow: 1;
        }

        .sidebar ul li:hover {
            background: #2c3e50;
        }

        /* Dropdown Menu */
        .dropdown .dropdown-content {
            display: none;
            flex-direction: column;
            background: #253347;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dropdown.active .dropdown-content {
            display: flex;
        }

        .dropdown-content a {
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }

        .dropdown-content a:hover {
            background: #3b4d66;
        }

        .dropdown-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dropdown-toggle span {
            font-size: 12px;
            opacity: 0.7;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 30px;
            width: 100%;
            transition: margin-left 0.3s ease;
        }

        .main-content h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        /* Mobile Sidebar */
        .menu-toggle {
            display: none;
            position: absolute;
            top: 20px;
            left: 20px;
            background: #1c2833;
            color: white;
            padding: 12px;
            border: none;
            cursor: pointer;
            font-size: 18px;
            z-index: 1000;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .menu-toggle:hover {
            background: #2c3e50;
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
                transform: translateX(-280px);
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
        <li><a href="index.php" aria-label="Go to Home">Home</a></li>

        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if($_SESSION['user_role'] == 'teacher'): ?>
                <li class="dropdown" aria-haspopup="true">
                    <a href="#" class="dropdown-toggle" aria-expanded="false">Actions <span>▼</span></a>
                    <div class="dropdown-content" aria-hidden="true">
                        <a href="create-assignment.php">Create Assignment</a>
                        <a href="view-submissions.php">View Submissions</a>
                        <a href="manage-classes.php">Manage Classes</a>
                    </div>
                </li>
            <?php else: ?>
                <li class="dropdown" aria-haspopup="true">
                    <a href="#" class="dropdown-toggle" aria-expanded="false">Actions <span>▼</span></a>
                    <div class="dropdown-content" aria-hidden="true">
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

<!-- Main Content -->
<!-- <div class="main-content">
    <h1>Welcome to the Assignment Collection System</h1>
    <p>This system helps students and teachers manage assignments effectively.</p>
</div> -->

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
                const parent = this.parentElement;
                const content = parent.querySelector('.dropdown-content');

                parent.classList.toggle('active');
                content.setAttribute('aria-hidden', content.classList.contains('active') ? 'false' : 'true');
                toggle.setAttribute('aria-expanded', parent.classList.contains('active') ? 'true' : 'false');

                // Close other dropdowns
                document.querySelectorAll('.dropdown').forEach(dropdown => {
                    if (dropdown !== parent) {
                        dropdown.classList.remove('active');
                        dropdown.querySelector('.dropdown-content').setAttribute('aria-hidden', 'true');
                        dropdown.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
                    }
                });
            });
        });
    });
</script>

</body>
</html>
