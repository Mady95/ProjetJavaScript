<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Charger les comptes de l'utilisateur
$stmt = $conn->prepare("SELECT id, account_name FROM accounts WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$accounts = $stmt->fetchAll();

// Gérer les filtres de période
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Charger l'historique des transactions avec filtre de période
$query = "SELECT t.transaction_date, t.transaction_type, t.amount, a.account_name 
          FROM transactions t 
          JOIN accounts a ON t.account_id = a.id 
          WHERE a.user_id = :user_id";

$params = [':user_id' => $user_id];

if ($start_date && $end_date) {
    $query .= " AND DATE(t.transaction_date) BETWEEN :start_date AND :end_date";
    $params[':start_date'] = $start_date;
    $params[':end_date'] = $end_date;
}

$query .= " ORDER BY t.transaction_date DESC LIMIT 50";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Gérer l'ajout de transactions (reste identique à votre code précédent)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $account_id = $_POST['account_id'];
    $transaction_type = $_POST['transaction_type'];
    $amount = floatval($_POST['amount']);

    // Vérification du compte
    $stmt = $conn->prepare("SELECT balance FROM accounts WHERE id = :account_id AND user_id = :user_id");
    $stmt->execute([':account_id' => $account_id, ':user_id' => $user_id]);
    $account = $stmt->fetch();

    if (!$account) {
        echo json_encode(['status' => 'error', 'message' => 'Compte invalide.']);
        exit();
    }

    $new_balance = $account['balance'];

    // Validation des montants
    if ($transaction_type === 'retrait' && $amount > $account['balance']) {
        echo json_encode(['status' => 'error', 'message' => 'Solde insuffisant.']);
        exit();
    }

    // Calcul du nouveau solde
    $new_balance = ($transaction_type === 'retrait') ? $new_balance - $amount : $new_balance + $amount;

    // Démarrer une transaction pour éviter les doublons
    $conn->beginTransaction();
    try {
        // Ajouter la transaction
        $stmt = $conn->prepare("INSERT INTO transactions (account_id, transaction_type, amount) VALUES (:account_id, :transaction_type, :amount)");
        $stmt->execute([
            ':account_id' => $account_id,
            ':transaction_type' => $transaction_type,
            ':amount' => $amount,
        ]);

        // Mettre à jour le solde du compte
        $stmt = $conn->prepare("UPDATE accounts SET balance = :new_balance WHERE id = :account_id");
        $stmt->execute([
            ':new_balance' => $new_balance,
            ':account_id' => $account_id,
        ]);
        

        $conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'Transaction ajoutée avec succès.', 'new_balance' => $new_balance]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'ajout de la transaction.']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Transaction</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="assets/js/scripts.js" defer></script>
</head>
<body>
    <header>
        <h1>Ajouter une Transaction</h1>
        <nav>
            <a href="dashboard.php">Retour au tableau de bord</a>
        </nav>
    </header>
    <main>
        <!-- Message de réponse -->
        <div id="response-message"></div>
        
        <!-- Formulaire de transaction -->
        <form id="transaction-form" method="post">
            <label for="account_id">Compte :</label><br>
            <select id="account_id" name="account_id" required>
                <option value="">-- Sélectionnez un compte --</option>
                <?php foreach ($accounts as $account): ?>
                    <option value="<?= $account['id'] ?>"><?= htmlspecialchars($account['account_name']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="transaction_type">Type de transaction :</label><br>
            <select id="transaction_type" name="transaction_type" required>
                <option value="dépôt">Dépôt</option>
                <option value="retrait">Retrait</option>
            </select><br><br>

            <label for="amount">Montant :</label><br>
            <input type="number" id="amount" name="amount" step="0.01" required><br><br>

            <button type="submit">Ajouter la Transaction</button>
        </form>

        <!-- Formulaire de filtre -->
        <h2 style="text-align:center;">Filtrer les Transactions par Période</h2>
        <form method="get" style="text-align:center; margin-bottom:20px;">
            <label for="start_date">Date de début :</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
            <label for="end_date">Date de fin :</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
            <button type="submit">Filtrer</button>
        </form>

        <!-- Historique des transactions -->
        <h2 style="text-align:center;">Historique des Transactions</h2>
        <!-- Bouton de téléchargement CSV -->
<form action="export_transactions.php" method="get" style="text-align:center; margin-top:20px;">
    <input type="hidden" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
    <input type="hidden" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
    <button type="submit" style="background-color: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
        Télécharger l'Historique (CSV)
    </button>
</form>

        <table border="1" cellpadding="10" style="width:100%; margin-top:20px;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Compte</th>
                </tr>
                
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?= htmlspecialchars($transaction['transaction_date']) ?></td>
                            <td><?= htmlspecialchars($transaction['transaction_type']) ?></td>
                            <td><?= number_format($transaction['amount'], 2) ?> €</td>
                            <td><?= htmlspecialchars($transaction['account_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center;">Aucune transaction récente.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            
        </table>
    </main>
    <footer>
        <p>&copy; 2024 Banque SBC. Tous droits réservés.</p>
    </footer>
</body>
</html>
