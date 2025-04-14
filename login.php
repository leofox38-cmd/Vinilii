<?php
// Imposta i cookie di sessione per una durata di 1 settimana
session_set_cookie_params(604800); // 1 settimana
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connessione al database fallita: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Verifica se l'utente esiste nel database
    $stmt = $dbh->prepare("SELECT * FROM Utenti WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Imposta la sessione e reindirizza
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit; // Interrompe lo script dopo il redirect
    } else {
        $error = "Credenziali non valide.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="form-container">
    <h2>Accedi</h2>
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
    </form>
    <p>Non hai un account? <a href="register.php">Registrati</a></p>
</div>
<script>
function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : null;
}
document.addEventListener("DOMContentLoaded", function() {
    if (getCookie("darkMode") === "true") {
        document.body.classList.add("dark-mode");
    }
});
</script>
</body>
</html>
