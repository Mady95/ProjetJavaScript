<?php
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Utilisateur non authentifié.']);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Vérifiez si l'ID du compte est passé
    if (!isset($_POST['account_id']) || empty($_POST['account_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Aucun compte spécifié.']);
        exit();
    }

    $account_id = (int)$_POST['account_id'];

    // Vérifiez si le compte appartient à l'utilisateur
    $stmt = $conn->prepare("SELECT id FROM accounts WHERE id = :account_id AND user_id = :user_id");
    $stmt->execute([':account_id' => $account_id, ':user_id' => $user_id]);
    $account = $stmt->fetch();

    if (!$account) {
        echo json_encode(['status' => 'error', 'message' => 'Compte introuvable ou non autorisé.']);
        exit();
    }

    try {
        // Supprimer les transactions associées
        $stmt = $conn->prepare("DELETE FROM transactions WHERE account_id = :account_id");
        $stmt->execute([':account_id' => $account_id]);

        // Supprimer le compte
        $stmt = $conn->prepare("DELETE FROM accounts WHERE id = :account_id");
        $stmt->execute([':account_id' => $account_id]);

        echo json_encode(['status' => 'success', 'message' => 'Compte supprimé avec succès.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression du compte.']);
    }
    exit();
}
?>
