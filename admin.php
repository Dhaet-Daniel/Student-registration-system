<?php
require_once __DIR__ . '/auth.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (attemptAdminLogin($username, $password)) {
        header('Location: dashboard.php');
        exit;
    }

    $errorMessage = 'Invalid admin username or password.';
}

if (isAdminAuthenticated()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page-shell narrow-shell">
        <section class="panel login-panel">
            <div class="panel-header">
                <div>
                    <p class="section-kicker">Admin Access</p>
                    <h2>Sign In to Dashboard</h2>
                </div>
                <a href="index.php" class="ghost-btn top-link">Back to Registration</a>
            </div>

            <?php if ($errorMessage !== ''): ?>
                <div class="status-banner error-banner"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Admin Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter admin username" required>
                </div>
                <div class="form-group">
                    <label for="password">Admin Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter admin password" required>
                </div>
                <button type="submit" class="primary-btn">Open Dashboard</button>
            </form>

            <p class="helper-text">Default credentials: <code>admin</code> / <code>admin123</code></p>
        </section>
    </div>
</body>
</html>
