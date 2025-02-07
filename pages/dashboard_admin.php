<?php
session_start();

// Vérifie si l'utilisateur est connecté et s'il est administrateur
if (!isset($_SESSION["utilisateur_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: acceuil.html"); // Redirection si non autorisé
    exit();
}

include("../config/database.php"); // Connexion à la base de données

// Gestion de l'ajout de nouveaux professeurs
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_prof'])) {
    $nom_prof = trim($_POST['nom_prof']);
    $prenom_prof = trim($_POST['prenom_prof']);
    $email_prof = trim($_POST['email_prof']);
    $password_prof = trim($_POST['password_prof']);

    if (!empty($nom_prof) && !empty($prenom_prof) && !empty($email_prof) && !empty($password_prof)) {
        // Hash du mot de passe
        $hashed_password = password_hash($password_prof, PASSWORD_DEFAULT);

        // Connexion à la base
        $conn = new mysqli("localhost", "root", "", "gestion_scolaire");

        if ($conn->connect_error) {
            die("Erreur de connexion : " . $conn->connect_error);
        }

        // Vérifie si l'email existe déjà
        $sql_check = "SELECT email FROM Utilisateur WHERE email=?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email_prof);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $_SESSION["error"] = "Cet email est déjà utilisé.";
        } else {
            // Insère le nouveau professeur
            $sql_insert = "INSERT INTO Utilisateur (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, 'professeur')";
            $stmt = $conn->prepare($sql_insert);
            $stmt->bind_param("ssss", $nom_prof, $prenom_prof, $email_prof, $hashed_password);
            
            if ($stmt->execute()) {
                $_SESSION["success"] = "Compte professeur ajouté avec succès !";
            } else {
                $_SESSION["error"] = "Erreur lors de l'ajout du compte.";
            }

            $stmt->close();
        }

        $stmt_check->close();
        $conn->close();
    } else {
        $_SESSION["error"] = "Veuillez remplir tous les champs.";
    }
}

// Suppression d'un professeur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_prof'])) {
    $prof_id = $_POST['prof_id'];

    $conn = new mysqli("localhost", "root", "", "gestion_scolaire");
    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }

    $sql_delete = "DELETE FROM Utilisateur WHERE utilisateur_id = ? AND role = 'professeur'";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $prof_id);
    
    if ($stmt_delete->execute()) {
        $_SESSION["success"] = "Professeur supprimé avec succès.";
    } else {
        $_SESSION["error"] = "Erreur lors de la suppression.";
    }

    $stmt_delete->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Ajouter Professeur</title>
    <link rel="stylesheet" href="../includes/styles.css">
</head>
<body>

<header>
    <h1>Tableau de bord Administrateur</h1>
    <a href="logout.php">Déconnexion</a>
</header>

<main>
    <h2>Ajouter un nouveau professeur</h2>

    <!-- Affichage des messages -->
    <?php
    if (isset($_SESSION["success"])) {
        echo "<p class='success-message'>" . $_SESSION["success"] . "</p>";
        unset($_SESSION["success"]);
    }
    if (isset($_SESSION["error"])) {
        echo "<p class='error-message'>" . $_SESSION["error"] . "</p>";
        unset($_SESSION["error"]);
    }
    ?>

    <form action="" method="POST">
        <label for="nom_prof">Nom du professeur :</label>
        <input type="text" id="nom_prof" name="nom_prof" required>

        <label for="prenom_prof">Prénom du professeur :</label>
        <input type="text" id="prenom_prof" name="prenom_prof" required>

        <label for="email_prof">Email du professeur :</label>
        <input type="email" id="email_prof" name="email_prof" required>

        <label for="password_prof">Mot de passe :</label>
        <input type="password" id="password_prof" name="password_prof" required>

        <button type="submit" name="add_prof">Créer le compte</button>
    </form>

    <h2>Liste des professeurs</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php
        // Connexion à la base pour récupérer la liste des professeurs
        $conn = new mysqli("localhost", "root", "", "gestion_scolaire");

        if ($conn->connect_error) {
            die("Erreur de connexion : " . $conn->connect_error);
        }

        $sql_prof = "SELECT utilisateur_id, nom, prenom, email FROM Utilisateur WHERE role = 'professeur'";
        $result_prof = $conn->query($sql_prof);

        if ($result_prof->num_rows > 0) {
            while ($row = $result_prof->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row["utilisateur_id"] . "</td>
                        <td>" . $row["nom"] . "</td>
                        <td>" . $row["prenom"] . "</td>
                        <td>" . $row["email"] . "</td>
                        <td>
                            <form method='POST' action=''>
                                <input type='hidden' name='prof_id' value='" . $row["utilisateur_id"] . "'>
                                <button type='submit' name='delete_prof' onclick='return confirm(\"Supprimer ce professeur ?\")'>Supprimer</button>
                            </form>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Aucun professeur enregistré.</td></tr>";
        }

        $conn->close();
        ?>
    </table>

</main>

</body>
</html>
