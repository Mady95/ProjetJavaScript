<?php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['account_id'], $_POST['threshold'])) {
    $account_id = $_POST['account_id'];
    $threshold = $_POST['threshold'];

    $stmt = $conn->prepare("UPDATE accounts SET threshold = :threshold WHERE id = :account_id");
    $stmt->execute([':threshold' => $threshold, ':account_id' => $account_id]);

    echo json_encode(['status' => 'success']);
    exit();
}
echo json_encode(['status' => 'error']);
