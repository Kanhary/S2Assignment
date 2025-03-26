<!-- home.php -->
<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'index'; // Default to 'index' if no page is selected
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-left: 250px; /* Sidebar width */
            padding: 20px;
        }
    </style>
</head>
<body>

    <!-- Include Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div id="container">
        <?php
        // Include the content based on the selected page
        if (file_exists($page . '.php')) {
            include($page . '.php');
        } else {
            echo "<h1>Page not found!</h1>";
        }
        ?>
    </div>

</body>
</html>
