<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica che l'utente sia loggato
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Verifica che sia stato fornito un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: carrello.php');
    exit;
}

$id = intval($_GET['id']);

// Rimuovi l'elemento dal carrello
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $key = array_search($id, $_SESSION['cart']);
    if ($key !== false) {
        unset($_SESSION['cart'][$key]);
        // Riordina gli indici dell'array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Reindirizza al carrello
header('Location: carrello.php');
exit;