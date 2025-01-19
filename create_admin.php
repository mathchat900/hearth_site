<?php
require 'db_connect.php';

try {
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (:nom, :email, :mot_de_passe, :role)");
    $stmt->execute([
        ':nom' => 'Admin',
        ':email' => 'admin@site.com',
        ':mot_de_passe' => password_hash('admin', PASSWORD_DEFAULT),
        ':role' => 'admin'
    ]);
    echo "Compte admin créé avec succès. Email : admin@site.com | Mot de passe : admin";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>