<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les comptes de l'utilisateur
$stmt = $conn->prepare("SELECT id, account_name, account_type, balance FROM accounts WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$accounts = $stmt->fetchAll();

// Préparer les transactions pour chaque compte
$transactions_by_account = [];
foreach ($accounts as $account) {
    $stmt = $conn->prepare("SELECT transaction_type, amount, transaction_date FROM transactions WHERE account_id = :account_id ORDER BY transaction_date DESC LIMIT 3");
    $stmt->execute([':account_id' => $account['id']]);
    $transactions_by_account[$account['id']] = $stmt->fetchAll();
}

// Calcul du solde total
$total_balance = array_sum(array_column($accounts, 'balance'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="assets/js/dashboard.js" defer></script>
</head>
<body>
    <header>
        <h1>Bienvenue dans votre tableau de bord</h1>
        <nav>
            <a href="add_account.php">Ajouter un compte bancaire</a>
            <a href="add_transaction.php">Effectuer une transaction</a>
            <a href="profile.php">Mon profil</a>
            <a href="historique.php">Historique connexion</a>
            <a href="logout.php">Déconnexion</a>
        </nav>
    </header>
    <main class="dashboard-container">

        <!-- Section Ajouter un compte -->
        <section class="dashboard-section">
            <h2>Ajouter un compte</h2>
            <p>Créez un nouveau compte bancaire.</p>
            <a href="add_account.php" class="btn">Ajouter</a>
        </section>

        <!-- Section Effectuer une transaction -->
        <section class="dashboard-section">
            <h2>Effectuer une transaction</h2>
            <p>Ajoutez une transaction pour l'un de vos comptes existants.</p>
            <a href="add_transaction.php" class="btn">Effectuer</a>
        </section>

        <!-- Section Liste des comptes -->
        <section class="dashboard-section">
            <h2>Vos comptes bancaires</h2>
            <ul class="account-list">
                <?php foreach ($accounts as $account): ?>
                    <li class="account-item">
                        <a href="#" data-account-id="<?= $account['id'] ?>" class="btn-delete" style="float:right; background-color:red; color:white; border-radius:5px; text-decoration: none; padding: 3px 6px;">
                            Supprimer le compte
                        </a>
                        <strong><?= htmlspecialchars($account['account_name']) ?></strong><br>
                        Type : <?= htmlspecialchars($account['account_type']) ?><br>
                        Solde : <?= number_format($account['balance'], 2) ?> €
                        <h4>Dernières transactions :</h4>
                        <ul class="transaction-list">
                            <?php if (!empty($transactions_by_account[$account['id']])): ?>
                                <?php foreach ($transactions_by_account[$account['id']] as $transaction): ?>
                                    <li>
                                        <?= htmlspecialchars($transaction['transaction_date']) ?> - <?= htmlspecialchars($transaction['transaction_type']) ?> : <?= number_format($transaction['amount'], 2) ?> €
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Aucune transaction récente.</li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h2>Solde total</h2>
            <p><strong><?= number_format($total_balance, 2) ?> €</strong></p>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Banque SBC. Tous droits réservés.</p>
    </footer>
</body>
</html>
