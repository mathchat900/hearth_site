<?php
// admin_panel.php

session_start();
require 'db_connect.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Gestion de l'ajout d'article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_article'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    try {
        $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, stock, image) VALUES (:nom, :description, :prix, :stock, :image)");
        $stmt->execute([
            ':nom' => $nom,
            ':description' => $description,
            ':prix' => $prix,
            ':stock' => $stock,
            ':image' => $image
        ]);
        $message = "Article ajouté avec succès.";
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}

// Gestion de la suppression d'article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_article'])) {
    $id = $_POST['article_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM produits WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $message = "Article supprimé avec succès.";
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}

// Gestion des annonces
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_annonce'])) {
    $titre = $_POST['titre'];
    $contenu = $_POST['contenu'];

    try {
        $stmt = $pdo->prepare("INSERT INTO annonces (titre, contenu) VALUES (:titre, :contenu)");
        $stmt->execute([':titre' => $titre, ':contenu' => $contenu]);
        $message = "Annonce ajoutée avec succès.";
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}

// Suppression des annonces
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_annonce'])) {
    $id = $_POST['annonce_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM annonces WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $message = "Annonce supprimée avec succès.";
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}

// Récupérer les articles et annonces
$articles = $pdo->query("SELECT * FROM produits ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$annonces = $pdo->query("SELECT * FROM annonces ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les logs
try {
    $logs = $pdo->query("SELECT * FROM commandes ORDER BY date_creation DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $logs = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="bg-primary text-white p-3">
        <h1>Admin Panel</h1>
    </header>

    <main class="container mt-5">
        <!-- Gestion des articles -->
        <section>
            <h2>Gestion des Articles</h2>
            <form method="POST" class="mb-5">
                <input type="hidden" name="ajouter_article" value="1">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" name="nom" id="nom" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="prix" class="form-label">Prix</label>
                    <input type="number" name="prix" id="prix" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" name="stock" id="stock" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image (URL)</label>
                    <input type="text" name="image" id="image" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>

            <h3>Liste des Articles</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td><?= $article['id'] ?></td>
                            <td><?= htmlspecialchars($article['nom']) ?></td>
                            <td><?= htmlspecialchars($article['description']) ?></td>
                            <td><?= $article['prix'] ?> €</td>
                            <td><?= $article['stock'] ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="supprimer_article" value="1">
                                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Gestion des annonces -->
        <section>
            <h2>Gestion des Annonces</h2>
            <form method="POST" class="mb-5">
                <input type="hidden" name="ajouter_annonce" value="1">
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre</label>
                    <input type="text" name="titre" id="titre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="contenu" class="form-label">Contenu</label>
                    <textarea name="contenu" id="contenu" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter Annonce</button>
            </form>

            <h3>Liste des Annonces</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Titre</th>
                        <th>Contenu</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($annonces as $annonce): ?>
                        <tr>
                            <td><?= $annonce['id'] ?></td>
                            <td><?= htmlspecialchars($annonce['titre']) ?></td>
                            <td><?= htmlspecialchars($annonce['contenu']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="supprimer_annonce" value="1">
                                    <input type="hidden" name="annonce_id" value="<?= $annonce['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>