<?php
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/SessionManager.php';
require_once __DIR__ . '/src/AuthService.php';

const DB_HOST = 'localhost';
const DB_NAME = 'excel_fix';
const DB_USER = 'root';
const DB_PASS = '';

$session = new SessionManager();
$session->start();

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$auth = new AuthService($db, $session);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isGuest = ($_POST['guest'] ?? '') === '1';

    if ($isGuest) {
        $session->loginAsGuest();
        $success = 'Je bent ingelogd als gast.';
    } else {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = 'Vul e-mail en wachtwoord in.';
        } elseif ($auth->loginUser($email, $password)) {
            $success = 'Je bent ingelogd.';
        } else {
            $error = 'Onjuiste inloggegevens.';
        }
    }
}

$loggedIn = $session->isLoggedIn();
$userEmail = $session->getEmail();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="Basics/css/style.css">
</head>
<body>
    <?php if ($loggedIn): ?>
        <div class="layout">
            <aside class="sidebar">
                <div class="brand">ExcelFix</div>
                <nav class="nav">
                    <a class="nav-link active" href="#">Home</a>
                    <a class="nav-link" href="#">Uploads</a>
                    <a class="nav-link" href="#">Templates</a>
                    <a class="nav-link" href="#">Support</a>
                </nav>
            </aside>
            <main class="main">
                <header class="page-header">
                    <h1>Home</h1>
                    <p class="helper">Fix, clean, and export your spreadsheets faster.</p>
                </header>

                <?php if ($error !== ''): ?>
                    <div class="alert error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php elseif ($success !== ''): ?>
                    <div class="alert success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <section class="home-grid">
                    <div class="upload-card">
                        <h2>Upload</h2>
                        <div class="upload-box">
                            <p>Drag and drop your file here</p>
                            <span class="helper">XLSX or CSV, max 25MB</span>
                            <button class="button" type="button" disabled>Upload</button>
                        </div>
                    </div>

                    <section class="card">
                        <h2>Account</h2>
                        <p>Ingelogd als <?php echo htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8'); ?>.</p>
                        <a class="button" href="logout.php">Logout</a>
                    </section>
                </section>
            </main>
        </div>
    <?php else: ?>
        <main class="container">
            <section class="card">
                <h1>Login</h1>

                <?php if ($error !== ''): ?>
                    <div class="alert error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php elseif ($success !== ''): ?>
                    <div class="alert success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <form method="post" class="form">
                    <label>E-mail
                        <input type="email" name="email" required>
                    </label>
                    <label>Wachtwoord
                        <input type="password" name="password" required>
                    </label>
                    <button type="submit" class="guest-text" name="guest" value="1" formnovalidate>Login als gast</button>
                    <button type="submit">Inloggen</button>
                </form>
                <p class="helper">Nog geen account? <a href="register.php">Meld je aan</a>.</p>
            </section>
        </main>
    <?php endif; ?>
</body>
</html>
