<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Non connecté.']);
    exit();
}

// Récupération des comptes de l'utilisateur
$stmt = $conn->prepare("SELECT id, account_name, balance FROM accounts WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Envoi des données en JSON
echo json_encode(['status' => 'success', 'accounts' => $accounts]);
exit();
