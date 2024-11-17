<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Validation simple
    if (empty($name) || empty($email)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Veuillez entrer une adresse email valide.";
    } else {
        // Mettre à jour les informations de l'utilisateur
        $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email WHERE id = :user_id");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':user_id' => $user_id
        ]);
        $success = "Vos informations ont été mises à jour avec succès.";
        // Mettre à jour les informations en session
        $_SESSION['user_name'] = $name;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Mon Profil</h1>
        <nav>
            <a href="dashboard.php">Retour au tableau de bord</a>
            <a href="logout.php">Déconnexion</a>
        </nav>
    </header>
    <main>
        <div class="profile-container">
            <h2>Modifier vos informations</h2>

            <?php if (isset($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <p style="color: green;"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <form action="" method="post">
                <label for="name">Nom :</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <button type="submit">Mettre à jour</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Banking App. Tous droits réservés.</p>
    </footer>
</body>
</html>
