<?php

session_start();

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit();
}

// Vérifier si le panier existe
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Gestion de la suppression d'un article du panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retirer_article'])) {
    $articleId = $_POST['article_id'];
    if (isset($_SESSION['panier'][$articleId])) {
        unset($_SESSION['panier'][$articleId]);
    }
}

// Calcul du total
$total = 0;
foreach ($_SESSION['panier'] as $article) {
    $total += $article['prix'] * $article['quantite'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Panier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header class="bg-primary text-white p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1>Votre Panier</h1>
            <nav>
                <a href="acheter.php" class="btn btn-light">Acheter</a>
                <a href="logout.php" class="btn btn-outline-light">Se déconnecter</a>
            </nav>
        </div>
    </header>

    <main class="container mt-5">
        <h2 class="mb-4">Articles dans votre panier</h2>
        <nav>
            <a href="acheter.php" class="btn btn-light">Acheter</a>
            <a href="logout.php" class="btn btn-outline-light">Se déconnecter</a>
        </nav>

        <?php if (empty($_SESSION['panier'])): ?>
            <div class="alert alert-warning">Votre panier est vide.</div>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Prix</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['panier'] as $id => $article): ?>
                        <tr>
                            <td><?= htmlspecialchars($article['nom']) ?></td>
                            <td><?= $article['prix'] ?> €</td>
                            <td><?= $article['quantite'] ?></td>
                            <td><?= $article['prix'] * $article['quantite'] ?> €</td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="retirer_article" value="1">
                                    <input type="hidden" name="article_id" value="<?= $id ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Retirer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="mt-4">
                <h3>Total : <?= $total ?> €</h3>
                <a href="valider_commande.php" class="btn btn-success">Valider la commande</a>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 - Site de Santé</p>
    </footer>
</body>

</html>