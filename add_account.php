<?php
// Démarrage de la session et connexion à la base de données
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config/db.php';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $account_name = $_POST['account_name'];
    $account_type = $_POST['account_type'];
    $user_id = $_SESSION['user_id'];

    // Validation simple
    if (empty($account_name) || empty($account_type)) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        // Insertion dans la base de données
        $stmt = $conn->prepare("INSERT INTO accounts (user_id, account_name, account_type, balance) VALUES (:user_id, :account_name, :account_type, 0)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':account_name' => $account_name,
            ':account_type' => $account_type
        ]);
        $success = "Compte bancaire ajouté avec succès.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un compte bancaire</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Ajouter un Compte Bancaire</h1>
        <nav>
            <a href="dashboard.php">Retour au tableau de bord</a>
        </nav>
    </header>
    <main>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p style="color: green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form action="" method="post">
            <label for="account_name">Nom du compte :</label>
            <input type="text" id="account_name" name="account_name" required>

            <label for="account_type">Type de compte :</label>
            <select id="account_type" name="account_type" required>
                <option value="courant">Courant</option>
                <option value="épargne">Épargne</option>
            </select>

            <button type="submit">Ajouter</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2024 Banque SBC. Tous droits réservés.</p>
    </footer>
</body>
</html>
