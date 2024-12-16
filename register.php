<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matric = $_POST['matric'];
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validation for name
    if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        die('Invalid name. Only letters and spaces are allowed.<br><a href="register.php">Back</a>');
    }

    // Validation for matric number
    if (!preg_match('/^[a-zA-Z0-9]{8}$/', $matric)) {
        die('Invalid matric number. Must be 8 alphanumeric characters.<br><a href="register.php">Back</a>');
    }

    // Validation for password
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/', $password)) {
        die('Password must be at least 8 characters long and include at least one letter, one number, and one special character.<br><a href="register.php">Back</a>');
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        die('Passwords do not match.<br><a href="register.php">Back</a>');
    }

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Insert into the database
    $stmt = $conn->prepare('INSERT INTO users (matric, name, password, role) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $matric, $name, $passwordHash, $role);

    if ($stmt->execute()) {
        header('Location: login.php');
        exit;
    } else {
        echo 'Error: ' . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <title>Register</title>
</head>
<body>
<div class="form-container">
    <h2>Registration</h2>
    <div class="card">
        <form action="register.php" method="POST">
            <!-- Matric Input -->
            <div class="input-group">
                <label for="matric"><i class="fas fa-id-card"></i></label>
                <input type="text" id="matric" name="matric" placeholder="Matric Number" required>
            </div>

            <!-- Name Input -->
            <div class="input-group">
                <label for="name"><i class="fas fa-user"></i></label>
                <input type="text" id="name" name="name" placeholder="Name" required>
            </div>

            <!-- Password Input -->
            <div class="input-group">
                <label for="password"><i class="fas fa-lock"></i></label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <!-- Confirm Password Input -->
            <div class="input-group">
                <label for="confirm-password"><i class="fas fa-lock"></i></label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <!-- Role Selection -->
            <div class="input-group radio-group">
                <label>
                    <input type="radio" name="role" value="Lecturer" required>
                    <span class="radio-label"><i class="fas fa-user-shield"></i> Lecturer</span>
                </label>
                <label>
                    <input type="radio" name="role" value="Student">
                    <span class="radio-label"><i class="fas fa-users"></i> Student</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="submit"><i class="fas fa-paper-plane"></i> Submit</button>
        </form>

        <p>Already have an account? <a href="login.php" class="link">Login Here!</a></p>
    </div>
</div>
</body>
</html>
