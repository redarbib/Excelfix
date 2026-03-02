<?php
session_start();

const DB_HOST = 'localhost';
const DB_NAME = 'excel_fix';
const DB_USER = 'root';
const DB_PASS = '';

function get_db(): PDO
{
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function normalize_email(string $email): string
{
    return strtolower(trim($email));
}

function login_user(string $email, string $password): bool
{
    $db = get_db();
    $email = normalize_email($email);
    $stmt = $db->prepare('SELECT id, email, password_hash FROM users WHERE LOWER(email) = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_email'] = $user['email'];
        return true;
    }

    return false;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isGuest = ($_POST['guest'] ?? '') === '1';

    if ($isGuest) {
        $_SESSION['user_id'] = 0;
        $_SESSION['user_email'] = 'gast';
        $success = 'Je bent ingelogd als gast.';
    } else {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = 'Vul e-mail en wachtwoord in.';
        } elseif (login_user($email, $password)) {
            $success = 'Je bent ingelogd.';
        } else {
            $error = 'Onjuiste inloggegevens.';
        }
    }
}

$loggedIn = isset($_SESSION['user_id']);
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
    <div class="layout">
        <aside class="sidebar" aria-hidden="true"></aside>
        <main class="container">
            <section class="card">
                <h1>Login</h1>

            <?php if ($error !== ''): ?>
                <div class="alert error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif ($success !== ''): ?>
                <div class="alert success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if ($loggedIn): ?>
                <p>Ingelogd als <?php echo htmlspecialchars($_SESSION['user_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>.</p>
                <a class="button" href="logout.php">Logout</a>
            <?php else: ?>
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
            <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
