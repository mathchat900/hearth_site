<?php
// ajouter_panier.php

session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produit_id'])) {
    $produit_id = (int) $_POST['produit_id'];
    $utilisateur_id = $_SESSION['utilisateur_id'];

    // Vérifier si le produit existe
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = :id");
    $stmt->execute([':id' => $produit_id]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produit) {
        // Ajouter le produit au panier
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        if (isset($_SESSION['panier'][$produit_id])) {
            $_SESSION['panier'][$produit_id]['quantite']++;
        } else {
            $_SESSION['panier'][$produit_id] = [
                'nom' => $produit['nom'],
                'prix' => $produit['prix'],
                'quantite' => 1
            ];
        }
        header('Location: panier.php');
        exit();
    } else {
        echo "Produit non trouvé.";
    }
}
?>