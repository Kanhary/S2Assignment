<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>University Portal</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --sidebar-bg: #ffff;
      --sidebar-hover: #334155;
      --text-light:  #334155;
      --accent:  #334155;
      --text-muted: #94a3b8;
      --font-main: 'Inter', sans-serif;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: var(--font-main);
    }

    body {
      display: flex;
      min-height: 100vh;
      background-color: #f8fafc;
    }

    .sidebar {width: 300px ;
      /* width: 26 0px; */
      background-color: var(--sidebar-bg);
      padding: 30px 20px;
      color: var(--text-light);
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar h2 {
      text-transform:uppercase;
      font-size: 20px;
      font-weight: 800;
      color: var(--accent);
      margin-bottom: 40px;
      /* text-align: center; */
      border-bottom:2px solid #000;
      padding-bottom:30px;

    }

    .sidebar nav a {
      
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: var(--text-light);
      padding: 12px 14px;
      border-radius: 8px;
      background:  rgb(237, 238, 241);
      transition: background 0.3s;
      border-left: 2px solid #000;
      font-size: 16px;
      margin-bottom: 10px;
      font-weight: bold;
    }

    .sidebar nav a:hover {
      background:  rgb(222, 223, 225);
    }

    .sidebar nav svg {
      width: 18px;
      height: 18px;
      stroke: var(--text-muted);
    }

    .main {
      margin-left: 250px;
      padding: 40px;
      flex: 1;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 1000;
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .main {
        margin-left: 0;
        padding: 20px;
      }

      .toggle-btn {
        position: fixed;
        top: 20px;
        left: 20px;
        background-color: var(--sidebar-bg);
        color: white;
        border: none;
        padding: 10px 12px;
        font-size: 18px;
        border-radius: 6px;
        z-index: 1001;
        cursor: pointer;
      }
    }
  </style>
</head>
<body>

<!-- Toggle Button for Mobile -->
<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <h2>Assignment Collection</h2>
  <nav>
    <a href="index.php">
      
      Dashboard
    </a>

    <?php if(isset($_SESSION['user_id'])): ?>
      <?php if($_SESSION['user_role'] == 'teacher'): ?>
        <a href="create-assignment.php">
          
          Create Assignment
        </a>
        <a href="view-submissions.php">
          
          View Submissions
        </a>
        <a href="manage-classes.php">
          
          Manange Class
        </a>
      <?php else: ?>
        <a href="my-assignments.php">
          
          My Assignments
        </a>
        <a href="my-submissions.php">
          
          My Submissions
        </a>
      <?php endif; ?>
      <a href="profile.php">
        
        Profile
      </a>
      <a href="logout.php">
        
        Logout
      </a>
    <?php else: ?>
      <a href="login.php">
        
        Login
      </a>
      <a href="register.php">
        
        Register
      </a>
    <?php endif; ?>
  </nav>
</aside>

<!-- Main Content -->
<!-- <div class="main">
  <h1>Welcome to the University Assignment System</h1>
</div> -->

<script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
  }
</script>

</body>
</html>
