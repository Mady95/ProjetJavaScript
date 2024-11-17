<?php
session_start();

// Vérifiez si la session est active
if (isset($_SESSION['user_id'])) {
    // Détruire toutes les variables de session
    session_unset();

    // Détruire la session
    session_destroy();
}

// Redirection vers la page de connexion
header("Location: login.php");
exit();
