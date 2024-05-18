<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ordinateur";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ajouter un nouvel ordinateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $marque = $_POST['marque'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];

    $sql = "INSERT INTO ordinateurs (marque, description, prix) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssd", $marque, $description, $prix);

    if ($stmt->execute()) {
        $message = "Nouvel ordinateur ajouté avec succès.";
    } else {
        $message = "Erreur : " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

// Supprimer un ordinateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $codeO = $_POST['codeO'];

    $sql = "DELETE FROM ordinateurs WHERE codeO = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codeO);

    if ($stmt->execute()) {
        $message = "Ordinateur supprimé avec succès.";
    } else {
        $message = "Erreur : " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Ordinateurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 50px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
        label, input, textarea, select, button {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 10px;
            background-color: #d9534f;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #c9302c;
        }
        .confirmation {
            text-align: center;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des Ordinateurs</h1>
        
        <?php if (isset($message)) { echo "<p class='confirmation'>$message</p>"; } ?>

        <form action="index.php" method="POST">
            <input type="hidden" name="action" value="add">
            <label for="marque">Marque :</label>
            <input type="text" id="marque" name="marque" required>

            <label for="description">Description :</label>
            <textarea id="description" name="description" required></textarea>

            <label for="prix">Prix :</label>
            <input type="number" step="0.01" id="prix" name="prix" required>

            <button type="submit">Ajouter</button>
        </form>

        <h2>Liste des Ordinateurs</h2>
        <table>
            <thead>
                <tr>
                    <th>Marque</th>
                    <th>Description</th>
                    <th>Prix</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM ordinateurs";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["marque"] . "</td>";
                        echo "<td>" . $row["description"] . "</td>";
                        echo "<td>" . $row["prix"] . "</td>";
                        echo "<td>
                            <form action='index.php' method='POST' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer cet ordinateur ?\");'>
                                <input type='hidden' name='action' value='delete'>
                                <input type='hidden' name='codeO' value='" . $row["codeO"] . "'>
                                <button type='submit'>Supprimer</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Aucun ordinateur trouvé</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
