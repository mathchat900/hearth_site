<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['utilisateur_id']) || empty($_SESSION['panier'])) {
    header('Location: acheter.php');
    exit();
}

$utilisateur_id = $_SESSION['utilisateur_id'];
$panier = $_SESSION['panier'];
$total = 0;

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO commandes (utilisateur_id, total) VALUES (:utilisateur_id, :total)");
    foreach ($panier as $id => $item) {
        $total += $item['prix'] * $item['quantite'];
    }
    $stmt->execute([':utilisateur_id' => $utilisateur_id, ':total' => $total]);

    $commande_id = $pdo->lastInsertId();

    foreach ($panier as $id => $item) {
        $stmt = $pdo->prepare("UPDATE produits SET stock = stock - :quantite WHERE id = :id AND stock >= :quantite");
        $stmt->execute([':quantite' => $item['quantite'], ':id' => $id]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("Stock insuffisant pour l'article : " . $item['nom']);
        }
    }

    $pdo->commit();
    unset($_SESSION['panier']);
    echo "<div class='alert alert-success'>Commande validée avec succès !</div>";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
}