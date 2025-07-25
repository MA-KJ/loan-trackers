<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Loan System') ?></title>
    <link rel="stylesheet" href="/public/assets/css/style.css">
</head>

<body>
    <header>
        <h2>Loan Tracker</h2>
        <nav>
            <a href="/public/dashboard.php">Dashboard</a>
            <a href="/public/logout.php">Logout</a>
        </nav>
    </header>
    <main>