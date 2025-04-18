<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>University Portal</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --sidebar-bg: #1e293b;
      --sidebar-hover: #334155;
      --text-light: #e2e8f0;
      --accent: #38bdf8;
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

    .sidebar {
      width: 250px;
      background-color: var(--sidebar-bg);
      padding: 30px 20px;
      color: var(--text-light);
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar h2 {
      font-size: 20px;
      font-weight: 600;
      color: var(--accent);
      margin-bottom: 40px;
      text-align: center;
    }

    .sidebar nav a {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: var(--text-light);
      padding: 12px 14px;
      border-radius: 8px;
      transition: background 0.3s;
      font-size: 15px;
      margin-bottom: 10px;
    }

    .sidebar nav a:hover {
      background-color: var(--sidebar-hover);
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
  <h2>University Portal</h2>
  <nav>
    <a href="index.php">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3"/>
      </svg>
      Dashboard
    </a>

    <?php if(isset($_SESSION['user_id'])): ?>
      <?php if($_SESSION['user_role'] == 'teacher'): ?>
        <a href="create-assignment.php">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v16m8-8H4"/>
          </svg>
          Create Assignment
        </a>
        <a href="view-submissions.php">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 17v-6h13M9 17h-3a4 4 0 010-8h3"/>
          </svg>
          View Submissions
        </a>
        <a href="manage-classes.php">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 17v-6h13M9 17h-3a4 4 0 010-8h3"/>
          </svg>
          Manange Class
        </a>
      <?php else: ?>
        <a href="my-assignments.php">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 20h9"/>
          </svg>
          My Assignments
        </a>
        <a href="my-submissions.php">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 20h9"/>
          </svg>
          My Submissions
        </a>
      <?php endif; ?>
      <a href="profile.php">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5.121 17.804A4 4 0 0112 21a4 4 0 016.879-3.196M12 7a4 4 0 100-8 4 4 0 000 8z"/>
        </svg>
        Profile
      </a>
      <a href="logout.php">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7"/>
        </svg>
        Logout
      </a>
    <?php else: ?>
      <a href="login.php">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
        Login
      </a>
      <a href="register.php">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 4v16m8-8H4"/>
        </svg>
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
