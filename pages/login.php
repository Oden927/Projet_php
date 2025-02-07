<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page de cionnexion du site de gestion des cours et absences.">
    <title>Connexion - Gestion Scolaire</title>
    <link rel="stylesheet" href="../includes/styles.css"> <!-- Lien vers le fichier CSS externe -->
    <!-- This section presents the platform -->
<section id="presentation">
    <h2>À propos de la plateforme</h2>
    <p>
        Cette plateforme permet aux élèves et aux professeurs de gérer les emplois du temps, les absences et les cours.
        Elle permettra également de déclarer des absences et de consulter l'emploi du temps.
    </p>
</section>

<!-- This section allows the user to log in -->
<section id="connexion">
    <h2>Connexion</h2>
    <form action="login.php" method="POST">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Se connecter</button>
    </form>
    <p><a href="forgot_password.php">Mot de passe oublié ?</a></p>
</section>
<?php
session_start();
include("../config/database.php"); // Connexion à la base

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Vérifie que les champs ne sont pas vides
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Veuillez remplir tous les champs.";
        header("Location: login.php");
        exit();
    }

    // **Compte par défaut pour l'admin**
    if ($email == "gfgdede@gmail.com" && $password == "0000") {
        $_SESSION["utilisateur_id"] = 9999; // ID fictif pour l'admin
        $_SESSION["role"] = "admin";
        header("Location: dashboard_admin.php"); // Redirection vers l'admin
        exit();
    }

    // Connexion à la base de données
    $conn = new mysqli("localhost", "root", "", "gestion_scolaire"); // Change si nécessaire
    if ($conn->connect_error) {
        die("Erreur de connexion: " . $conn->connect_error);
    }

    // Sécurisation des données utilisateur
    $email = $conn->real_escape_string($email);

    // Vérification de l'utilisateur dans la base de données
    $sql = "SELECT utilisateur_id, password, role FROM Utilisateur WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Vérifie si le mot de passe correspond
        if (password_verify($password, $row['password'])) {
            $_SESSION["utilisateur_id"] = $row["utilisateur_id"];
            $_SESSION["role"] = $row["role"];

            // Redirige tous les utilisateurs normaux (élève et professeur) vers l'accueil
            header("Location: acceuil.html");
            exit();
        } else {
            $_SESSION['error'] = "Mot de passe incorrect.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Aucun compte trouvé avec cet email.";
        header("Location: login.php");
        exit();
    }

    // Fermeture de la connexion
    $stmt->close();
    $conn->close();
}
?>



<!-- Footer with copyright information -->
<footer>
    <p>&copy; 2025 Gestion Scolaire - Tous droits réservés.</p>
</footer>
