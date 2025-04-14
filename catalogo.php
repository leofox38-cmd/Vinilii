<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

try {
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
} catch (PDOException $e) {
    die("Connessione al database fallita: " . $e->getMessage());
}

// Prima recuperiamo i generi disponibili dalla tabella Catalogo
$queryGeneri = "SELECT DISTINCT Genere FROM Catalogo WHERE Genere IS NOT NULL AND Genere != ''";
$stmtGeneri = $dbh->prepare($queryGeneri);
$stmtGeneri->execute();
$generi = $stmtGeneri->fetchAll(PDO::FETCH_COLUMN);

// Gestione dei filtri e cookie
$filtroGenere = '';

// Se è stato selezionato un genere specifico
if (isset($_GET['genere']) && !empty($_GET['genere'])) {
    $filtroGenere = $_GET['genere'];
    // Salva il filtro genere nei cookie per 30 giorni
    setcookie('last_genre_filter', $filtroGenere, time() + 3600 * 24 * 30, '/');
} 
// Altrimenti se c'è un filtro genere nei cookie, usalo
elseif (isset($_COOKIE['last_genre_filter'])) {
    $filtroGenere = $_COOKIE['last_genre_filter'];
}

// Gestione del filtro di ricerca generale (già presente nel tuo codice)
$searchTerm = '';
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $searchTerm = $_GET['q'];
    setcookie('last_search_filter', $searchTerm, time() + 3600 * 24 * 30, '/');
} elseif (isset($_COOKIE['last_search_filter']) && empty($filtroGenere)) {
    $searchTerm = $_COOKIE['last_search_filter'];
}

$table = 'Catalogo';

// Costruzione della query in base ai filtri attivi
if (!empty($filtroGenere)) {
    // Filtro per genere specifico
    $query = "SELECT * FROM $table WHERE Genere = :genere";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':genere', $filtroGenere, PDO::PARAM_STR);
} elseif (!empty($searchTerm)) {
    // Filtro per termine di ricerca generale
    $query = "SELECT * FROM $table 
              WHERE Titolo LIKE :term 
                 OR Artista LIKE :term 
                 OR Genere LIKE :term";
    $stmt = $dbh->prepare($query);
    $like = "%$searchTerm%";
    $stmt->bindParam(':term', $like, PDO::PARAM_STR);
} else {
    // Nessun filtro: mostra tutti i prodotti
    $query = "SELECT * FROM $table";
    $stmt = $dbh->prepare($query);
}

// Esegui la query
$stmt->execute();
$prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogo Vinili - VinylStore</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navbar -->
    <?php include('header.php'); ?>

    <div class="container">
        <h1 class="my-4 text-center">Il Nostro Catalogo Vinili</h1>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <!-- Form di ricerca -->
                <form action="catalogo.php" method="get" class="d-flex">
                    <input class="form-control me-2" type="search" name="q" placeholder="Cerca..." aria-label="Cerca"
                           value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    <button class="btn btn-outline-light" type="submit">Cerca</button>
                </form>
            </div>
            <div class="col-md-6">
                <!-- Menu a tendina per generi -->
                <form action="catalogo.php" method="get" class="d-flex">
                    <select name="genere" class="form-select me-2" aria-label="Seleziona genere">
                        <option value="">-- Seleziona un genere --</option>
                        <?php foreach ($generi as $genere): ?>
                            <option value="<?php echo htmlspecialchars($genere); ?>" 
                                    <?php echo $filtroGenere === $genere ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($genere); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-outline-light" type="submit">Filtra</button>
                </form>
            </div>
        </div>
        
        <!-- Mostra filtri attivi -->
        <?php if (!empty($filtroGenere) || !empty($searchTerm)): ?>
            <div class="active-filters mb-3">
                <strong>Filtri attivi:</strong>
                <?php if (!empty($filtroGenere)): ?>
                    <span class="badge bg-primary me-2">Genere: <?php echo htmlspecialchars($filtroGenere); ?></span>
                <?php endif; ?>
                <?php if (!empty($searchTerm)): ?>
                    <span class="badge bg-primary me-2">Ricerca: <?php echo htmlspecialchars($searchTerm); ?></span>
                <?php endif; ?>
                <!-- Fix rimuovi filtri - ora usa JavaScript per cancellare anche i cookie -->
                <button class="btn btn-sm btn-danger" onclick="clearFilters()">Rimuovi filtri</button>
            </div>
        <?php endif; ?>
        
        <div class="catalogo-container">
            <?php if (count($prodotti) === 0): ?>
                <p class="text-center">Nessun vinile trovato.</p>
            <?php else: ?>
                <?php foreach ($prodotti as $prodotto): ?>
                    <div class="item">
                        <div class="item-image">
                            <?php if (!empty($prodotto['Immagine'])): ?>
                                <img src="<?php echo htmlspecialchars($prodotto['Immagine']); ?>" 
                                     alt="<?php echo htmlspecialchars($prodotto['Titolo']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="item-details">
                            <h5 class="item-name"><?php echo htmlspecialchars($prodotto['Titolo']); ?></h5>
                            <div class="item-info">
                                <p>
                                    <strong>Artista:</strong> <?php echo htmlspecialchars($prodotto['Artista']); ?><br>
                                    <strong>Genere:</strong> <?php echo htmlspecialchars($prodotto['Genere']); ?><br>
                                    <strong>Prezzo:</strong> €<?php echo number_format($prodotto['Prezzo'], 2, ',', '.'); ?>
                                </p>
                                <p class="availability <?php echo strtolower(str_replace(' ', '-', $prodotto['Disponibilità'])); ?>">
                                    <strong>Disponibilità:</strong> <?php echo htmlspecialchars($prodotto['Disponibilità']); ?>
                                </p>
                            </div>
                            <a href="dettaglio.php?id=<?php echo $prodotto['ID']; ?>" class="btn btn-primary">Dettagli</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer -->
    <?php include('footer.php'); ?>

    <!-- Script per cancellare i cookie quando si rimuovono i filtri -->
    <script>
    function clearFilters() {
        // Cancella i cookie di filtro
        document.cookie = "last_genre_filter=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "last_search_filter=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        
        // Reindirizza alla pagina del catalogo senza parametri
        window.location.href = "catalogo.php";
    }
    </script>
</body>
</html>