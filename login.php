<?php
session_start();
include 'db.php';

$error = ''; // Variable to hold error messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $matric = filter_var($_POST['matric'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    // Validate matric number format (example: 8 alphanumeric characters)
    if (!preg_match('/^[a-zA-Z0-9]{8}$/', $matric)) {
        $error = 'Invalid matric number format.';
    } else {
        // Prepare and execute query
        $stmt = $conn->prepare('SELECT matric, password, role FROM users WHERE matric = ?');
        $stmt->bind_param('s', $matric);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID
            session_regenerate_id();

            // Set session variables
            $_SESSION['user_matric'] = $user['matric'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect to the main page
            header('Location: main.php');
            exit();
        } else {
            $error = 'Invalid matric number or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <div class="card">
            <!-- Display error message if any -->
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <!-- Matric Input -->
                <div class="input-group">
                    <label for="matric"><i class="fas fa-id-card"></i></label>
                    <input type="text" id="matric" name="matric" placeholder="Matric Number" required>
                </div>

                <!-- Password Input -->
                <div class="input-group">
                    <label for="password"><i class="fas fa-lock"></i></label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="submit"><i class="fas fa-paper-plane"></i> Submit</button>
            </form>

            <p>Don't have an account? <a href="/WebDev/Lab5b/register.php" class="link">Register Here!</a></p>
        </div>
    </div>
</body>
</html>
