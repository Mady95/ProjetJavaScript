<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config/db.php';

// Récupération des transactions pour un compte spécifique
if (isset($_GET['account_id'])) {
    $account_id = $_GET['account_id'];
    $user_id = $_SESSION['user_id'];

    // Vérifier si le compte appartient à l'utilisateur
    $stmt = $conn->prepare("SELECT account_name FROM accounts WHERE id = :account_id AND user_id = :user_id");
    $stmt->execute([':account_id' => $account_id, ':user_id' => $user_id]);
    $account = $stmt->fetch();

    if (!$account) {
        die("Compte non trouvé ou non autorisé.");
    }

    $stmt = $conn->prepare("SELECT * FROM transactions WHERE account_id = :account_id ORDER BY transaction_date DESC");
    $stmt->execute([':account_id' => $account_id]);
    $transactions = $stmt->fetchAll();
} else {
    die("Aucun compte sélectionné.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Transactions</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Historique des Transactions</h1>
        <nav>
            <a href="dashboard.php">Retour au tableau de bord</a>
        </nav>
    </header>
    <main>
        <h2><?= $account['account_name'] ?></h2>

        <?php if ($transactions): ?>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?= $transaction['transaction_date'] ?></td>
                            <td><?= ucfirst($transaction['transaction_type']) ?></td>
                            <td><?= number_format($transaction['amount'], 2) ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune transaction trouvée pour ce compte.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2024 Banque SBC. Tous droits réservés.</p>
    </footer>
</body>
</html>
