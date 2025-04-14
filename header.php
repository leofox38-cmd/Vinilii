<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>VinylStore</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    // Funzione per leggere i cookie
    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
    }
    
    // Controlla se il tema scuro è attivo
    document.addEventListener("DOMContentLoaded", function() {
        if (getCookie("darkMode") === "true") {
            document.body.classList.add("dark-mode");
        }
    });
    </script>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="navbar-brand"><a href="index.php" style="text-decoration: none; color: inherit;">VinylStore</a></div>
            <?php if(isset($_SESSION['username'])): ?>
                <div class="user-greeting">
                    Ciao, <?= htmlspecialchars($_SESSION['username']) ?>!
                </div>
            <?php endif; ?>
            <div class="navbar-nav">
                <a class="nav-link nav-button" href="index.php">Home</a>
                <a class="nav-link nav-button" href="catalogo.php">Catalogo</a>
                <?php if(isset($_SESSION['username'])): ?>
                    <a class="nav-link nav-button" href="carrello.php">Carrello</a>
                    <a class="nav-link nav-button" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link nav-button" href="login.php">Login</a>
                    <a class="nav-link nav-button" href="register.php">Registrati</a>
                <?php endif; ?>
                <button id="darkModeToggle" class="nav-link nav-button">Tema scuro</button>
            </div>
        </nav>
    </header>
<script>
// Funzione per alternare il tema scuro e salvare la preferenza in un cookie
document.getElementById("darkModeToggle").addEventListener("click", function() {
    document.body.classList.toggle("dark-mode");
    let darkModeEnabled = document.body.classList.contains("dark-mode");
    let d = new Date();
    d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000)); // 1 settimana
    document.cookie = "darkMode=" + (darkModeEnabled ? "true" : "false") + ";expires=" + d.toUTCString() + ";path=/";
    this.textContent = darkModeEnabled ? "Tema chiaro" : "Tema scuro";
});

// Aggiorna il testo del pulsante al caricamento
document.addEventListener("DOMContentLoaded", function() {
    const darkModeToggle = document.getElementById("darkModeToggle");
    if (document.body.classList.contains("dark-mode")) {
        darkModeToggle.textContent = "Tema chiaro";
    } else {
        darkModeToggle.textContent = "Tema scuro";
    }
});
</script>