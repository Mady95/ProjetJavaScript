<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['account_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Paramètres invalides.']);
    exit();
}

$account_id = $_GET['account_id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT transaction_date, transaction_type, amount FROM transactions WHERE account_id = :account_id ORDER BY transaction_date DESC");
$stmt->execute([':account_id' => $account_id]);

$transactions = $stmt->fetchAll();

if ($transactions) {
    echo json_encode(['status' => 'success', 'transactions' => $transactions]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Aucune transaction trouvée.']);
}
