<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Collection System</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            /* background-color: #f0f4f8; */
            margin: 0;
            padding: 0;
            display: flex;
            /* justify-content: center;
            align-items: center; */
           
        }

        /* Container to hold the sidebar and main content */
        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Main content area */
        main {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: margin-left 0.3s ease;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        h3 {
            color: #34495e;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        /* Dashboard container */
        .dashboard {
            padding: 20px;
            border-radius: 8px;
            background-color: #ecf0f1;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Buttons */
        .btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .btn-small {
            background-color: #e67e22;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            margin-top: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-small:hover {
            background-color: #d35400;
        }

        /* Assignments list */
        .assignments-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .assignment-card {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            width: calc(33% - 20px);
            box-sizing: border-box;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .assignment-card:hover {
            transform: translateY(-5px);
        }

        .assignment-card h4 {
            color: #2c3e50;
            margin: 0;
        }

        .assignment-card p {
            color: #7f8c8d;
        }

        .due-date {
            font-weight: bold;
        }

        .status {
            margin-top: 10px;
        }

        .pending {
            color: #e74c3c;
        }

        .submitted {
            color: #2ecc71;
        }

        /* Welcome section */
        .welcome {
            text-align: center;
            margin-top: 50px;
        }

        .welcome h2 {
            font-size: 2rem;
            color: #2c3e50;
        }

        .auth-buttons {
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            main {
                margin-left: 0;
                padding: 10px;
            }

            .assignments-list {
                flex-direction: column;
            }

            .assignment-card {
                width: 100%;
            }

            .dashboard-actions {
                flex-direction: column;
                gap: 10px;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .assignment-actions {
                display: flex;
                flex-direction: column;
            }

            .btn-small {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
    
</head>
<body>
    
        <?php include 'includes/sidebar.php'; ?>

        <main>
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['user_role'] == 'teacher'): ?>
                    <div class="dashboard">
                        <h2>Teacher Dashboard</h2>
                        <div class="dashboard-actions">
                            <a href="create-assignment.php" class="btn">Create New Assignment</a>
                            <a href="view-submissions.php" class="btn">View Submissions</a>
                        </div>
                        
                        <h3>Your Assignments</h3>
                        <?php 
                            $assignments = getTeacherAssignments($_SESSION['user_id']);
                            if(count($assignments) > 0):
                        ?>
                        <div class="assignments-list">
                            <?php foreach($assignments as $assignment): ?>
                                <div class="assignment-card">
                                    <h4><?php echo htmlspecialchars($assignment['title']); ?></h4>
                                    <p><?php echo htmlspecialchars(substr($assignment['description'], 0, 100)) . '...'; ?></p>
                                    <p class="due-date">Due: <?php echo htmlspecialchars($assignment['due_date']); ?></p>
                                    <div class="assignment-actions">
                                        <a href="view-assignment.php?id=<?php echo $assignment['id']; ?>" class="btn-small">View</a>
                                        <a href="edit-assignment.php?id=<?php echo $assignment['id']; ?>" class="btn-small">Edit</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                            <p>No assignments created yet.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="dashboard">
                        <h2>Student Dashboard</h2>
                        
                        <h3>Pending Assignments</h3>
                        <?php 
                            $assignments = getStudentAssignments($_SESSION['user_id']);
                            if(count($assignments) > 0):
                        ?>
                        <div class="assignments-list">
                            <?php foreach($assignments as $assignment): ?>
                                <div class="assignment-card">
                                    <h4><?php echo htmlspecialchars($assignment['title']); ?></h4>
                                    <p><?php echo htmlspecialchars(substr($assignment['description'], 0, 100)) . '...'; ?></p>
                                    <p class="due-date">Due: <?php echo htmlspecialchars($assignment['due_date']); ?></p>
                                    <p class="status">Status: 
                                        <?php 
                                            if(isSubmitted($_SESSION['user_id'], $assignment['id'])) {
                                                echo '<span class="submitted">Submitted</span>';
                                            } else {
                                                echo '<span class="pending">Pending</span>';
                                            }
                                        ?>
                                    </p>
                                    <div class="assignment-actions">
                                        <a href="view-assignment.php?id=<?php echo $assignment['id']; ?>" class="btn-small">View</a>
                                        <?php if(!isSubmitted($_SESSION['user_id'], $assignment['id'])): ?>
                                            <a href="submit-assignment.php?id=<?php echo $assignment['id']; ?>" class="btn-small">Submit</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                            <p>No pending assignments.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="welcome">
                    <h2>Assignment Collection System</h2>
                    <p>An organized and efficient platform for managing university assignments.</p>
                    <div class="auth-buttons">
                        <a href="login.php" class="btn btn-primary">Login</a>
                        <a href="register.php" class="btn btn-secondary">Register</a>
                    </div>
                </div>
            <?php endif; ?>
        </main>

        <!-- <?php include 'includes/footer.php'; ?> -->

    <!-- <script src="assets/js/script.js"></script> -->
</body>
</html>