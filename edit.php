<?php
include('module.php');
$conn = dbconnect();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Values ändern</title>
    <link rel="stylesheet" href="darksite.css"/>
</head>
<body>
    
<?php
anmelden($conn);
// Überprüfen, ob die GET-Variablen gesetzt sind
if(angemeldet($conn) == "Admin" || angemeldet($conn) == "Moderator"){
    if(isset($_GET['tabelle']) && isset($_GET['id'])) {
        $tabelle = removetagsbyuml(removeleerzeichen($_GET['tabelle']));
        $id = $_GET['id'];
        if($id != 1 && $tabelle != "Gruppe"){
            if ($tabelle == "Gruppe" && angemeldet($conn) != "Admin"){
                $redirect_page = 'https://xn--kpenickralley-imb.de/creategroup.php';
                header('Location:'  .$redirect_page);
                die();
            }
            // SQL-Abfrage vorbereiten
            $sql = "SELECT * FROM $tabelle WHERE ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows > 0) {
                // Daten ausgeben
                echo "<form action='update_script.php' method='get'>";
                echo "<table>";
                while($row = $result->fetch_assoc()) {
                    foreach($row as $key => $value) {
                        echo "<tr>";
                        echo "<td>$key</td>";
                        if ($value != "Admin"){
                            if($key != 'Passwort') {
                                echo "<td><input type='text' name='$key' value='$value'></td>";
                            } else {
                                echo "<td>********</td>";
                            }
                        } else {
                            echo "<td> Einem Admin kann die Rolle nicht entzogen werden! Versuchen sie es mit einer Löschung </td>";
                        }
                        echo "</tr>";
                    }
                }
                echo "</table>";
                echo "<input type='hidden' name='tabelle' value='$tabelle'>";
                echo "<input type='hidden' name='id' value='$id'>";
                echo "<input type='submit' value='Ändern'>";
                echo "</form>";
            } else {
                echo "Keine Daten gefunden.";
            }
            $stmt->close();
        }else {
            echo "Dieser User ist wichtig für die Funktionalität des Systems. Die Löschung/Rollenänderung ist deshalb nicht möglich!";
        }
    } else {
        echo "GET-Variablen 'tabelle' oder 'id' nicht gesetzt.";
    }
}else {
    echo "anmeldung ungültig";
}
?>
</body>
</html>