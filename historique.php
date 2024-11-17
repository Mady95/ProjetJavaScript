<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer l'historique des connexions
$stmt = $conn->prepare("SELECT login_time, ip_address FROM login_history WHERE user_id = :user_id ORDER BY login_time DESC LIMIT 50");
$stmt->execute([':user_id' => $user_id]);
$login_history = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des connexions</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Historique des connexions</h1>
        <nav>
            <a href="dashboard.php">Retour au tableau de bord</a>
            <a href="logout.php">Déconnexion</a>
        </nav>
    </header>
    <main class="dashboard-container">
        <section class="dashboard-section">
            <h2>Connexions récentes</h2>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Date et Heure</th>
                        <th>Adresse IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($login_history)): ?>
                        <?php foreach ($login_history as $login): ?>
                            <tr>
                                <td><?= htmlspecialchars($login['login_time']) ?></td>
                                <td><?= htmlspecialchars($login['ip_address']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">Aucune connexion récente.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Banque SBC. Tous droits réservés.</p>
    </footer>
</body>
</html>
