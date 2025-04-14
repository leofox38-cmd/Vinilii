<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Reindirizza alla pagina di login se l'utente non è loggato
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Controlla se il carrello esiste ed è vuoto
$carelloVuoto = !isset($_SESSION['cart']) || empty($_SESSION['cart']);

if (!$carelloVuoto) {
    try {
        $dbh = new PDO('sqlite:database.db');
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $ids = implode(',', array_map('intval', array_unique($_SESSION['cart'])));
        $query = "SELECT * FROM Catalogo WHERE ID IN ($ids)";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Errore: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrello - VinylStore</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navbar -->
    <?php include('header.php'); ?>

    <div class="container">
        <h1 class="my-4 text-center">Il Tuo Carrello</h1>
        
        <?php if ($carelloVuoto): ?>
            <div class="text-center mb-4">
                <p>Il tuo carrello è vuoto.</p>
                <a href="catalogo.php" class="btn btn-primary">Vai al Catalogo</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Immagine</th>
                            <th>Prodotto</th>
                            <th>Artista</th>
                            <th>Prezzo</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totale = 0;
                        foreach ($prodotti as $prodotto): 
                            $totale += $prodotto['Prezzo'];
                        ?>
                            <tr>
                                <td>
                                    <?php if (!empty($prodotto['Immagine'])): ?>
                                        <img src="<?php echo htmlspecialchars($prodotto['Immagine']); ?>" 
                                            alt="<?php echo htmlspecialchars($prodotto['Titolo']); ?>"
                                            style="max-height: 80px;">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($prodotto['Titolo']); ?></td>
                                <td><?php echo htmlspecialchars($prodotto['Artista']); ?></td>
                                <td>€<?php echo number_format($prodotto['Prezzo'], 2, ',', '.'); ?></td>
                                <td>
                                    <a href="dettaglio.php?id=<?php echo $prodotto['ID']; ?>" class="btn btn-sm btn-info">Dettagli</a>
                                    <a href="togli_elemento.php?id=<?php echo $prodotto['ID']; ?>" class="btn btn-sm btn-danger">Rimuovi</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Totale:</strong></td>
                            <td>€<?php echo number_format($totale, 2, ',', '.'); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="catalogo.php" class="btn btn-secondary">Continua lo Shopping</a>
                <a href="checkout.php" class="btn btn-success">Procedi al Checkout</a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Footer -->
    <?php include('footer.php'); ?>
</body>
</html>