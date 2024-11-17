<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Vérifier les filtres de période
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Charger les transactions
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

$query .= " ORDER BY t.transaction_date DESC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Définir le nom du fichier CSV
$filename = "transactions_" . date("Ymd_His") . ".csv";

// En-têtes pour le téléchargement
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");

// Ouvrir la sortie en écriture
$output = fopen("php://output", "w");

// Ajouter les en-têtes de colonnes
fputcsv($output, ["Date", "Type", "Montant (€)", "Compte"]);

// Ajouter les lignes des transactions
foreach ($transactions as $transaction) {
    fputcsv($output, [
        $transaction['transaction_date'],
        $transaction['transaction_type'],
        number_format($transaction['amount'], 2),
        $transaction['account_name'],
    ]);
}

// Fermer la sortie
fclose($output);
exit();
