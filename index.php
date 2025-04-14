<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// File: index.php

try {
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
} catch (PDOException $e) {
    die("Connessione al database fallita: " . $e->getMessage());
}

// Query per selezionare gli ultimi 3 vinili aggiunti
$table = 'Catalogo';
$query = "SELECT * FROM $table ORDER BY ID DESC LIMIT 3";
$stmt = $dbh->prepare($query);
$stmt->execute();
$ultime_novita = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VinylStore - La tua musica in vinile</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navbar -->
    <?php include('header.php'); ?>
    
    <!-- Contenuto principale -->
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section text-center">
            <div class="container">
                <h1>Benvenuti su VinylStore</h1>
                <p class="lead">La tua destinazione per vinili di qualità</p>
                <a href="catalogo.php" class="btn btn-danger btn-lg mt-3">Scopri il Catalogo</a>
            </div>
        </div>
        
        <!-- Sezione Chi Siamo -->
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="mb-4">La Nostra Passione per i Vinili</h2>
                <p class="lead">
                    VinylStore nasce dalla passione per la musica e per il fascino intramontabile del vinile. 
                    Offriamo una selezione curata di album classici e nuove uscite, per soddisfare sia i collezionisti esperti 
                    che chi si avvicina per la prima volta al mondo dei vinili.
                </p>
                <p>
                    Ogni disco nel nostro catalogo è stato scelto con cura, garantendo la migliore qualità audio e condizioni perfette. 
                    Esplora la nostra collezione e riscopri il piacere di ascoltare la musica come una volta, con il calore 
                    e la profondità che solo il vinile sa offrire.
                </p>
            </div>
        </div>
        
        <!-- Ultime Novità -->
        <h2 class="text-center mb-4">Le Nostre Ultime Novità</h2>
        <div class="row">
            <?php foreach ($ultime_novita as $prodotto): ?>
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <?php if (!empty($prodotto['Immagine'])): ?>
                            <img src="<?php echo htmlspecialchars($prodotto['Immagine']); ?>" alt="<?php echo htmlspecialchars($prodotto['Titolo']); ?>" class="img-fluid">
                        <?php endif; ?>
                        <h4><?php echo htmlspecialchars($prodotto['Titolo']); ?></h4>
                        <p><strong>Artista:</strong> <?php echo htmlspecialchars($prodotto['Artista']); ?></p>
                        <p><strong>Prezzo:</strong> €<?php echo number_format($prodotto['Prezzo'], 2, ',', '.'); ?></p>
                        <a href="dettaglio.php?id=<?php echo $prodotto['ID']; ?>" class="btn btn-primary">Scopri di più</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Perché Scegliere Noi -->
        <div class="row mt-5">
            <div class="col-12 text-center mb-4">
                <h2>Perché Scegliere VinylStore</h2>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box text-center">
                    <h4>Qualità Garantita</h4>
                    <p>Tutti i nostri vinili sono selezionati attentamente e verificati per garantire una riproduzione audio impeccabile.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box text-center">
                    <h4>Ampia Selezione</h4>
                    <p>Dal rock al jazz, dal pop al classico: la nostra collezione spazia attraverso tutti i generi musicali.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box text-center">
                    <h4>Consulenza Esperta</h4>
                    <p>Il nostro team di appassionati è sempre pronto a consigliarti e aiutarti a trovare il vinile perfetto per te.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <?php include('footer.php'); ?>
    
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
