<?php
session_start();

// Initialize tasks array in session if not set
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Add new task
if (isset($_POST['new_task']) && $_POST['new_task'] != '') {
    $_SESSION['tasks'][] = $_POST['new_task'];
    header("Location: index.php");
    exit();
}

// Delete task
if (isset($_GET['delete'])) {
    $index = $_GET['delete'];
    if (isset($_SESSION['tasks'][$index])) {
        unset($_SESSION['tasks'][$index]);
        $_SESSION['tasks'] = array_values($_SESSION['tasks']); // Reindex array
    }
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple PHP To-Do App</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        ul { list-style: none; padding: 0; }
        li { padding: 8px 0; }
        a { color: red; text-decoration: none; margin-left: 10px; }
        input[type=text] { padding: 5px; width: 250px; }
        input[type=submit] { padding: 5px 10px; }
    </style>
</head>
<body>
    <h1>Simple PHP To-Do App</h1>

    <form method="post" action="">
        <input type="text" name="new_task" placeholder="Add a new task">
        <input type="submit" value="Add">
    </form>

    <ul>
        <?php foreach ($_SESSION['tasks'] as $index => $task): ?>
            <li>
                <?php echo htmlspecialchars($task); ?>
                <a href="?delete=<?php echo $index; ?>">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
