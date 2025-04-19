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
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->

    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f4f6f9;
        color: #2c3e50;
    }

    .container {
        display: flex;
        min-height: 100vh;
    }

    main {
        margin-left: 250px;
        padding: 40px;
        width: 100%;
        transition: margin-left 0.3s ease;
    }

    h2 {
        font-size: 2rem;
        margin-bottom: 20px;
        color: #34495e;
    }

    h3 {
        font-size: 1.5rem;
        margin: 30px 0 20px;
        color: #2c3e50;
    }

    .dashboard {
        padding: 30px;
        /* background-color: #ffffff; */
        border-radius: 16px;
        /* box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05); */
    }

    .btn, .btn-small {
        display: inline-block;
        padding: 14px 50px;
        /* border-radius: 8px; */
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        text-align: center;
        cursor: pointer;
    }

    .btn {
        background:rgb(9, 29, 55);
        color: white;
    }

    .btn:hover {
        background:rgba(9, 29, 55, 0.7);
    }

    .btn-small {
        background:rgb(9, 29, 55);
        color: white;
        padding: 8px 14px;
    }

    .btn-small:hover {
        background:rgba(9, 29, 55, 0.7);
    }

    .btn-primary {
        background:rgb(9, 29, 55);
        color: white;

    }

    .btn-primary:hover {
        background:rgba(9, 29, 55, 0.7);
    }

    .btn-secondary {
        background-color: #bdc3c7;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #95a5a6;
    }

    .assignments-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        /* max-width: 350px; */

    }

    .assignment-card {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        max-width: 350px;
        box-shadow: 0 4px 20px rgba(31, 45, 84, 0.26);
        border-left:5px solid  rgb(30, 42, 76);

    }

    .assignment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }

    .assignment-card h4 {
        font-size: 1.2rem;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    .assignment-card p {
        color: #7f8c8d;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .due-date {
        font-weight: bold;
        color: #34495e;
    }

    .status {
        margin-top: 10px;
        font-size: 0.95rem;
    }

    .pending {
        color: #e74c3c;
    }

    .submitted {
        color: #27ae60;
    }

    .assignment-actions {
        margin-top: 15px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .dashboard-actions {
        margin-bottom: 30px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .welcome {
        text-align: center;
        margin-top: 100px;
    }

    .welcome h2 {
        font-size: 3rem;
        color: #2c3e50;
    }

    .welcome p {
        margin: 20px auto;
        color: #555;
        font-size: 1.1rem;
        max-width: 600px;
    }

    .auth-buttons {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    /* Responsive */
    @media (max-width: 768px) {
        main {
            margin-left: 0;
            padding: 20px;
        }

        .btn, .btn-small {
            width: 100%;
        }

        .dashboard-actions, .assignment-actions {
            flex-direction: column;
        }

        .welcome {
            margin-top: 60px;
        }

        .welcome h2 {
            font-size: 2.2rem;
        }
        .login{
            background:rgba(9, 29, 55, 0.7);
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
                    <h2>Welcome, Teacher</h2>
                    <div class="dashboard-actions">
                        <a href="create-assignment.php" class="btn">Create New Assignment</a>
                        <a href="view-submissions.php" class="btn">View Submissions</a>
                    </div>

                    <h3>Your Assignments</h3>
                    <?php $assignments = getTeacherAssignments($_SESSION['user_id']); ?>
                    <?php if(count($assignments) > 0): ?>
                        <div class="assignments-list">
                            <?php foreach($assignments as $assignment): ?>
                                <div class="assignment-card">
                                    <h4><?= htmlspecialchars($assignment['title']) ?></h4>
                                    <p><?= htmlspecialchars(substr($assignment['description'], 0, 100)) ?>...</p>
                                    <p class="due-date">Due: <?= htmlspecialchars($assignment['due_date']) ?></p>
                                    <div class="assignment-actions">
                                        <a href="view-assignment.php?id=<?= $assignment['id'] ?>" class="btn-small">View</a>
                                        <a href="edit-assignment.php?id=<?= $assignment['id'] ?>" class="btn-small">Edit</a>
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
                <h2>Welcome, <?= htmlspecialchars($user['name'] ?? 'Guest') ?></h2>

                    <!-- <h3>Pending Assignments</h3> -->
                    <?php $assignments = getStudentAssignments($_SESSION['user_id']); ?>
                    <?php if(count($assignments) > 0): ?>
                        <div class="assignments-list">
                            <?php foreach($assignments as $assignment): ?>
                                <div class="assignment-card">
                                    <h4><?= htmlspecialchars($assignment['title']) ?></h4>
                                    <p><?= htmlspecialchars(substr($assignment['description'], 0, 100)) ?>...</p>
                                    <p class="due-date">Due: <?= htmlspecialchars($assignment['due_date']) ?></p>
                                    <p class="status">
                                        Status:
                                        <?php if(isSubmitted($_SESSION['user_id'], $assignment['id'])): ?>
                                            <span class="submitted">Submitted</span>
                                        <?php else: ?>
                                            <span class="pending">Pending</span>
                                        <?php endif; ?>
                                    </p>
                                    <div class="assignment-actions">
                                        <a href="view-assignment.php?id=<?= $assignment['id'] ?>" class="btn-small">View</a>
                                        <?php if(!isSubmitted($_SESSION['user_id'], $assignment['id'])): ?>
                                            <a href="submit-assignment.php?id=<?= $assignment['id'] ?>" class="btn-small">Submit</a>
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
                <h2>Assignment <br/>Collection System</h2>
                <p>An organized and efficient platform for managing university assignments.</p>
                <!-- <div class="auth-buttons">
                    <a href="login.php" class="btn login">Login</a>
                    <a href="register.php" class="btn btn-secondary">Register</a>
                </div> -->
            </div>
        <?php endif; ?>
    </main>

</body>
</html>
