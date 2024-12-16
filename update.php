<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentMatric = $_POST['currentMatric'];
    $newMatric = trim($_POST['newMatric']);
    $newName = trim($_POST['newName']);
    $newRole = $_POST['newRole'];

    // Input validation
    if (!preg_match('/^[a-zA-Z\s]+$/', $newName)) {
        die('Invalid name. Only letters and spaces are allowed.');
    }

    if (!preg_match('/^[a-zA-Z0-9]{8}$/', $newMatric)) {
        die('Invalid matric number. Must be 8 alphanumeric characters.');
    }

    // Update query
    $stmt = $conn->prepare('UPDATE users SET name = ?, matric = ?, role = ? WHERE matric = ?');
    $stmt->bind_param('ssss', $newName, $newMatric, $newRole, $currentMatric);

    if ($stmt->execute()) {
        echo 'User updated successfully. <a href="main.php">Back to dashboard</a>.';
    } else {
        echo 'Error: ' . htmlspecialchars($stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Fetch the current data for the user
if (!isset($_GET['matric'])) {
    die('Invalid request.');
}

$currentMatric = $_GET['matric'];

$stmt = $conn->prepare('SELECT matric, name, role FROM users WHERE matric = ?');
$stmt->bind_param('s', $currentMatric);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die('User not found.');
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="style.css">
    <title>Update User</title>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Update User Information</h2>
            <form method="POST">
                <!-- Current Matric -->
                <input type="hidden" name="currentMatric" value="<?= htmlspecialchars($user['matric']); ?>">

                <!-- New Matric -->
                <div class="input-group">
                    <label for="newMatric">New Matric:</label>
                    <input type="text" id="newMatric" name="newMatric" value="<?= htmlspecialchars($user['matric']); ?>" required>
                </div>

                <!-- New Name -->
                <div class="input-group">
                    <label for="newName">New Name:</label>
                    <input type="text" id="newName" name="newName" value="<?= htmlspecialchars($user['name']); ?>" required>
                </div>

                <!-- New Role -->
                <div class="input-group">
                    <label for="newRole">Role:</label>
                    <select id="newRole" name="newRole" required>
                        <option value="student" <?= $user['role'] === 'student' ? 'selected' : ''; ?>>Student</option>
                        <option value="lecturer" <?= $user['role'] === 'lecturer' ? 'selected' : ''; ?>>Lecturer</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <button type="submit">Update</button>
            </form>
            <a href="/WebDev/Lab5b/main.php" class="link">Cancel</a>
        </div>
    </div>
</body>
</html>

