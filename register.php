<?php
// register.php
session_set_cookie_params(604800);
session_start();
try {
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connessione al database fallita: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $dbh->prepare("INSERT INTO Utenti (username, password) VALUES (?, ?)");
        if ($stmt->execute([$username, $password_hash])) {
            $_SESSION['user'] = $username;
            header("Location: catalogo.php");
            exit;
        } else {
            $error = "Registrazione fallita. Username potrebbero già esistere.";
        }
    } else {
        $error = "Compila tutti i campi.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registrazione</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="form-container">
    <h2>Registrati</h2>
    <?php if(isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="register.php">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>


        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Registrati</button>
    </form>
    <p>Hai già un account? <a href="login.php">Accedi</a></p>
</div>
<script>
// Gestione del tema scuro tramite cookie (come in login.php)
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
