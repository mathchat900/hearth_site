<?php
// index.php

session_start();
require 'db_connect.php';

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['utilisateur_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Récupérer les statistiques
try {
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
    $totalArticles = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
    $totalOrders = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
} catch (PDOException $e) {
    $totalUsers = $totalArticles = $totalOrders = 0;
}

// Récupérer les annonces
try {
    $annonces = $pdo->query("SELECT * FROM annonces ORDER BY date_creation DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $annonces = [];
}

//$maintenanceStatus = $pdo->query("SELECT statut FROM maintenance WHERE id = 1")->fetchColumn();
//if ($maintenanceStatus && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
//    echo "<h1>Site en maintenance</h1><p>Le site est actuellement indisponible. Veuillez revenir plus tard.</p>";
//    exit();
//}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site de Santé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        header {
            background: linear-gradient(45deg, #007bff, #6610f2);
        }

        header h1 {
            font-size: 2.5rem;
        }

        .nav-link {
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #ffc107 !important;
        }

        .card {
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .section-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: #007bff;
            margin: 10px auto 0;
        }

        footer {
            background-color: #343a40;
            color: #ffffff;
        }

        .chart-container {
            position: relative;
            height: 400px;
        }
    </style>
</head>

<body>
    <header class="text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1>Site de Santé</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a href="index.php" class="nav-link text-white">Accueil</a></li>
                    <li class="nav-item"><a href="acheter.php" class="nav-link text-white">Acheter</a></li>
                    <li class="nav-item"><a href="info.php" class="nav-link text-white">Infos Pharmacie</a></li>
                    <?php if ($isAdmin): ?>
                        <li class="nav-item"><a href="admin.php" class="nav-link text-white">Admin Panel</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div>
                <?php if ($isLoggedIn): ?>
                    <a href="logout.php" class="btn btn-outline-light">Se déconnecter</a>
                    <a href="panier.php" class="btn btn-light">Panier (<span
                            id="cart-count"><?= isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0 ?></span>)</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-light">Se connecter</a>
                    <a href="signup.php" class="btn btn-outline-light">Créer un compte</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container my-5">
        <!-- Section Annonces -->
        <section id="annonces" class="mb-5">
            <h2 class="section-title text-center">Annonces</h2>
            <div class="row">
                <?php if (!empty($annonces)): ?>
                    <?php foreach ($annonces as $annonce): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h4 class="card-title"><?= htmlspecialchars($annonce['titre']) ?></h4>
                                    <p class="card-text"><?= nl2br(htmlspecialchars($annonce['contenu'])) ?></p>
                                    <p class="text-muted"><small>Publié le
                                            <?= date('d/m/Y à H:i', strtotime($annonce['date_creation'])) ?></small></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">Aucune annonce pour le moment.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Section Statistiques -->
        <section id="stats" class="mb-5">
            <h2 class="section-title text-center">Statistiques</h2>
            <div class="row text-center my-4">
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h3>Total des utilisateurs</h3>
                            <p class="display-6"><?= $totalUsers ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h3>Articles en stock</h3>
                            <p class="display-6"><?= $totalArticles ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h3>Commandes effectuées</h3>
                            <p class="display-6"><?= $totalOrders ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="stats-chart"></canvas>
            </div>
        </section>
    </main>

    <footer class="text-center py-3">
        <p>&copy; 2025 - Site de Santé</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('stats-chart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Utilisateurs', 'Articles', 'Commandes'],
                    datasets: [{
                        label: 'Statistiques',
                        data: [<?= $totalUsers ?>, <?= $totalArticles ?>, <?= $totalOrders ?>],
                        backgroundColor: ['#4CAF50', '#FFC107', '#2196F3'],
                        borderColor: ['#388E3C', '#FFA000', '#1976D2'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>