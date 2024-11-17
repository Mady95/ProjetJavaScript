<?php
require 'config/db.php';

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ip_list[0]); // Prendre la première IP si plusieurs sont listées
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    } else {
        return 'IP inconnue';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];

        // Insérer l'historique de connexion
        $ip_address = getUserIP();
        $stmt = $conn->prepare("INSERT INTO login_history (user_id, login_time, ip_address) VALUES (:user_id, NOW(), :ip_address)");
        $stmt->execute([
            ':user_id' => $user['id'],
            ':ip_address' => $ip_address,
        ]);

        header("Location: dashboard.php");
        exit();
    } else {
        echo "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Banque SBC</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <h2>Connectez-vous sur votre compte Banque SBC ! </h2>
        </nav>
    </header>
    <main class="main-banner">
        <div class="overlay"></div>
        <div class="banner-content">
            <div class="login-container">
                <h2>Connexion</h2>
                <form class="login-form" action="" method="post">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Mot de passe" required>
                    <button type="submit">Se connecter</button>
                    <a href="register.php">Pas encore de compte ? Inscrivez-vous</a>
                </form>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Banque SBC. Tous droits réservés.</p>
    </footer>
</body>
</html>


