<?php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Vérifiez la longueur du mot de passe
    if (strlen($password) < 8) {
        echo "<script>alert('Le mot de passe doit contenir au moins 8 caractères.');</script>";
    } else {
        // Hachez le mot de passe et insérez les données dans la base
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hashed_password]);

        header("Location: login.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Banque SBC</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <a href="login.php">Connexion</a>
            <a href="register.php">Inscription</a>
        </nav>
    </header>
    <main class="main-banner">
        <div class="overlay"></div>
        <div class="banner-content">
            <div class="register-container">
                <h2>Créer un compte</h2>
              <form class="register-form" action="" method="post">
                    <input type="text" name="name" placeholder="Nom" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Mot de passe" required minlength="8" 
                           title="Le mot de passe doit contenir au moins 8 caractères.">
                    <button type="submit">S'inscrire</button>
             </form>

            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Banque SBC. Tous droits réservés.</p>
    </footer>
</body>
</html>
