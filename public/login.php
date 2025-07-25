<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';

session_start();
$auth = new AuthController($pdo);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($auth->login($username, $password)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}

$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="/loan-tracker/public/assets/css/style.css">
</head>

<body>

    <header>
        <h2>Loan Tracker</h2>
        <nav>
            <a href="/loan-tracker/public/login.php">🔐 Login</a>
        </nav>
    </header>

    <main>
        <section style="max-width: 400px; margin: 40px auto; padding: 20px; background: white; border-radius: 8px;">
            <h3 style="text-align: center;">🔐 Admin Login</h3>

            <?php if ($error): ?>
                <p style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post">
                <label>Username:</label>
                <input type="text" name="username" required style="width: 100%; padding: 8px;"><br><br>

                <label>Password:</label>
                <input type="password" name="password" required style="width: 100%; padding: 8px;"><br><br>

                <button type="submit" style="width: 100%; padding: 10px; background: #0066cc; color: white; border: none; border-radius: 4px;">
                    Login
                </button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Loan Tracker System</p>
    </footer>

</body>

</html>