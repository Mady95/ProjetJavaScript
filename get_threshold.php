<?php
require 'config/db.php';

if (isset($_GET['account_id'])) {
    $account_id = $_GET['account_id'];
    $stmt = $conn->prepare("SELECT threshold FROM accounts WHERE id = :account_id");
    $stmt->execute([':account_id' => $account_id]);
    $result = $stmt->fetch();

    echo json_encode(['threshold' => $result ? $result['threshold'] : null]);
    exit();
}
echo json_encode(['threshold' => null]);
