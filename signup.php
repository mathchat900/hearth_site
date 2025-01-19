<?php
// signup.php

require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (:nom, :email, :mot_de_passe)");
        $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':mot_de_passe' => $mot_de_passe
        ]);
        echo "Inscription réussie ! Vous pouvez maintenant vous connecter.";
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            echo "Cet email est déjà utilisé.";
        } else {
            echo "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Inscription</h3>
                        <form method="POST" action="signup.php">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" name="nom" id="nom" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="mot_de_passe" class="form-label">Mot de passe</label>
                                <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                        </form>
                        <p class="text-center mt-3">Déjà inscrit ? <a href="login.php">Connectez-vous</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>