<?php
session_start();
include 'db.php';

// Session timeout duration in seconds (5 minutes)
$timeout = 300;

// Check if session has expired
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
    // Session expired
    session_unset();
    session_destroy();
    header('Location: login.php?message=Session expired. Please log in again.');
    exit();
}

// Update last activity time
$_SESSION['LAST_ACTIVITY'] = time();

// Ensure user is logged in
if (!isset($_SESSION['user_matric'])) {
    header('Location: login.php');
    exit();
}

// Fetch all users from the database
$result = $conn->query('SELECT matric, name, role FROM users');

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $matricToDelete = $_POST['delete'];
    $stmt = $conn->prepare('DELETE FROM users WHERE matric = ?');
    $stmt->bind_param('s', $matricToDelete);
    $stmt->execute();
    header('Location: main.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user_matric']); ?></h2>
        <h3>All Users</h3>
        <table border="1">
            <thead>
                <tr>
                    <th>Matric</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['matric']); ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['role']); ?></td>
                    <td>
                        <a href="update.php?matric=<?= urlencode($row['matric']); ?>" class="up-link">Update</a>
                        <form method="POST" style="display:inline;">
                            <button type="submit" name="delete" class="del" value="<?= htmlspecialchars($row['matric']); ?>">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <footer>
            <p><a href="/WebDev/Lab5b/login.php" class="logOut">Log Out</a></p>
        </footer>
    </div>
</body>
</html>
