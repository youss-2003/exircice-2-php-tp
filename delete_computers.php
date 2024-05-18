<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['marque'])) {
    $marque = $data['marque'];

    $servername = "localhost";
    $username = "root";
    $password = ""; 
    $dbname = "ordinateur";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "DELETE FROM ordinateurs WHERE marque = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $marque);

    if ($stmt->execute()) {
        echo json_encode("Suppression effectuée avec succès !");
    } else {
        echo json_encode("Erreur lors de la suppression : " . $conn->error);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode("Aucune marque spécifiée.");
}
?>
