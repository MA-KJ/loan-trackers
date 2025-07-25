<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Loan System') ?></title>
    <link rel="stylesheet" href="/loan-tracker/public/assets/css/style.css">
</head>

<body>
    <header>
        <h2>Loan Tracker</h2>
        <nav>
            <a href="/loan-tracker/public/dashboard.php">🏠 Dashboard</a>
            <a href="/loan-tracker/views/dashboard/add_loan.php">➕ Add New Loan</a>
            <a href="/loan-tracker/views/dashboard/reports.php">📊 Reports</a>
            <a href="/loan-tracker/public/logout.php">🔒 Logout</a>
        </nav>

    </header>
    <main>