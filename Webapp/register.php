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

function email_exists(PDO $db, string $email): bool
{
    $stmt = $db->prepare('SELECT 1 FROM users WHERE LOWER(email) = :email LIMIT 1');
    $stmt->execute([':email' => normalize_email($email)]);
    return (bool) $stmt->fetchColumn();
}

function create_user(PDO $db, string $email, string $password): void
{
    $stmt = $db->prepare('INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)');
    $stmt->execute([
        ':email' => normalize_email($email),
        ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ]);
}

$error = '';
$success = '';
$email = '';
$password = '';
$confirm = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($email === '' || $password === '' || $confirm === '') {
        $error = 'Vul alle velden in.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'E-mail is ongeldig.';
    } elseif ($password !== $confirm) {
        $error = 'Wachtwoorden komen niet overeen.';
    } elseif (strlen($password) < 8) {
        $error = 'Wachtwoord moet minstens 8 tekens zijn.';
    } else {
        $db = get_db();
        if (email_exists($db, $email)) {
            $error = 'E-mail bestaat al.';
        } else {
            create_user($db, $email, $password);
            $success = 'Account aangemaakt. Je kan nu inloggen.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>
    <link rel="stylesheet" href="Basics/css/style.css">
</head>
<body>
    <div class="layout">
        <aside class="sidebar" aria-hidden="true"></aside>
        <main class="container">
            <section class="card">
                <h1>Registreren</h1>

            <?php if ($error !== ''): ?>
                <div class="alert error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif ($success !== ''): ?>
                <div class="alert success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if ($success === ''): ?>
                <form method="post" class="form">
                    <label>E-mail
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </label>
                    <label>Wachtwoord
                        <input type="password" name="password" value="<?php echo htmlspecialchars($password, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </label>
                    <label>Herhaal wachtwoord
                        <input type="password" name="confirm" value="<?php echo htmlspecialchars($confirm, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </label>
                    <button type="submit">Account maken</button>
                </form>
            <?php endif; ?>

                <p class="helper">Heb je al een account? <a href="index.php">Inloggen</a>.</p>
            </section>
        </main>
    </div>
</body>
</html>
