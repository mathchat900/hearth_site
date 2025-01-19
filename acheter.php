<?php
require 'db_connect.php';
session_start();

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM produits");
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acheter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header class="bg-primary text-white p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1>Catalogue des Produits</h1>
            <nav>
                <a href="index.php" class="btn btn-light">Home</a>
                <a href="panier.php" class="btn btn-light">Panier</a>
                <a href="logout.php" class="btn btn-outline-light">Se déconnecter</a>
            </nav>
        </div>
    </header>

    <main class="container mt-5">
        <section>
            <h2 class="text-center mb-4">Produits disponibles</h2>
            <div class="row">
                <?php if ($produits): ?>
                    <?php foreach ($produits as $produit): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <img src="<?= htmlspecialchars($produit['image']) ?>" class="card-img-top"
                                    alt="Image de <?= htmlspecialchars($produit['nom']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($produit['nom']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($produit['description']) ?></p>
                                    <p class="card-text"><strong>Prix : <?= htmlspecialchars($produit['prix']) ?> €</strong></p>
                                    <form method="POST" action="ajouter_panier.php">
                                        <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
                                        <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="alert alert-warning text-center">Aucun produit disponible pour le moment.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2025 - Site de Santé</p>
    </footer>
</body>

</html>