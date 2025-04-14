<?php
// File: dettaglio.php

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][] = $id;
    header('Location: carrello.php');
    exit;
}

try {
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
} catch (PDOException $e) {
    die("Connessione al database fallita: " . $e->getMessage());
}

// Verifica che sia stato fornito un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: catalogo.php');
    exit;
}

$id = intval($_GET['id']);

// Query per selezionare il prodotto specifico
$table = 'Catalogo';
$query = "SELECT * FROM $table WHERE ID = :id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$prodotto = $stmt->fetch(PDO::FETCH_ASSOC);

// Se il prodotto non esiste, reindirizza al catalogo
if (!$prodotto) {
    header('Location: catalogo.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($prodotto['Titolo']); ?> - VinylStore</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navbar -->
    <?php include('header.php'); ?>
    
    <div class="container">
        <div class="row product-detail">
            <div class="col-md-5">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($prodotto['Immagine']); ?>" 
                    alt="<?php echo htmlspecialchars($prodotto['Titolo']); ?>" 
                    style="max-width: 90%; max-height: 600px; display: block; margin: 0 auto; border-radius: 10px; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
            </div>
            </div>
            <div class="col-md-7">
                <div class="product-info">
                    <h2><?php echo htmlspecialchars($prodotto['Titolo']); ?></h2>
                    <p><strong>Artista:</strong> <?php echo htmlspecialchars($prodotto['Artista']); ?></p>
                    <p><strong>Genere:</strong> <?php echo htmlspecialchars($prodotto['Genere']); ?></p>
                    
                    <?php if(isset($prodotto['Anno']) && !empty($prodotto['Anno'])): ?>
                        <p><strong>Anno:</strong> <?php echo htmlspecialchars($prodotto['Anno']); ?></p>
                    <?php endif; ?>
                    
                    <?php if(isset($prodotto['Etichetta']) && !empty($prodotto['Etichetta'])): ?>
                        <p><strong>Etichetta:</strong> <?php echo htmlspecialchars($prodotto['Etichetta']); ?></p>
                    <?php endif; ?>
                    
                    <div class="price">€<?php echo number_format($prodotto['Prezzo'], 2, ',', '.'); ?></div>
                    
                    <p class="availability 
                        <?php 
                        if ($prodotto['Disponibilità'] > 0) echo 'in-stock';
                        elseif ($prodotto['Disponibilità'] = 0) echo 'low-stock';
                        else echo 'out-of-stock';
                        ?>">
                        <strong>Disponibilità:</strong> <?php echo htmlspecialchars($prodotto['Disponibilità']); ?>
                    </p>
                    
                    <?php if(isset($prodotto['Descrizione']) && !empty($prodotto['Descrizione'])): ?>
                        <h4>Descrizione</h4>
                        <p><?php echo nl2br(htmlspecialchars($prodotto['Descrizione'])); ?></p>
                    <?php endif; ?>
                    
                    <div class="mt-4">
                        <a href="catalogo.php" class="btn btn-secondary">Torna al Catalogo</a>
                        <?php if ($prodotto['Disponibilità'] != 'Non disponibile'): ?>
                            <form action="dettaglio.php?id=<?php echo $prodotto['ID']; ?>" method="post">
                                <input type="hidden" name="id" value="<?php echo $prodotto['ID']; ?>">
                                <button type="submit" class="btn btn-primary">Aggiungi al Carrello</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <?php include('footer.php'); ?>
    


    
</body>
</html>
