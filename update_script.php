<?php
include('module.php');
$conn = dbconnect();
loginmaske($conn);
if(angemeldet($conn) != "Admin" && angemeldet($conn) != "Moderator"){
    $redirect_page = 'https://xn--kpenickralley-imb.de/creategroup.php';
    header('Location:'  .$redirect_page);
    die();
}
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
// Überprüfen, ob das Formular gesendet wurde
if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['tabelle']) && isset($_GET['id'])) {
    $tabelle = $_GET['tabelle'];
    $id = $_GET['id'];
    $updateData = [];

    // Alle übermittelten Daten sammeln, außer 'tabelle' und 'id'
    foreach($_GET as $key => $value) {
        if($key != 'tabelle' && $key != 'id' && $key != 'Passwort') {
            $updateData[$key] = $value;
        }
    }

    // SQL-Update-Statement vorbereiten
    $setPart = [];
    foreach($updateData as $key => $value) {
        $setPart[] = "$key = ?";
    }
    $setPart = implode(', ', $setPart);

    $sql = "UPDATE $tabelle SET $setPart WHERE ID = ?";
    $stmt = $conn->prepare($sql);

    // Parameter binden
    $types = str_repeat('s', count($updateData)) . 'i';
    $params = array_merge(array_values($updateData), array($id));
    $stmt->bind_param($types, ...$params);

    // SQL-Statement ausführen
    if($stmt->execute()) {
        echo "Die Daten wurden erfolgreich aktualisiert.";
        $redirect_page = 'https://xn--kpenickralley-imb.de/adminlinks.html';
        header('Location:'  .$redirect_page);
        die();
        
    } else {
        echo "Fehler beim Aktualisieren der Daten: " . $stmt->error;
    }

    $stmt->close();
    
} else {
    echo "Fehler: Formulardaten sind nicht korrekt übermittelt worden.";
}

$conn->close();
?>
