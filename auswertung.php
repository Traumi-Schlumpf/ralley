<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="darksite.css"/>
    <link rel="stylesheet" href="addfragen.css"/>
    <title>Auswertungsseite</title>
</head>
<body>
<?php
$ende=true;
include('module.php');
$conn = dbconnect();
$ende = vergleicheZeiten($beispielzeit);

if ($ende) {
    // Zeige eine andere Seite basierend auf der Variable $ende an
    echo "<h2>Auswertung der Korrekturen pro Gruppe</h2>";
    
    echo "<table>";
    echo "<tr>";
    echo "<th>Platz</th>";
    echo "<th>Gruppe</th>";
    echo "<th>Ausstehend</th>";
    echo "<th>Richtig</th>";
    echo "<th>Falsch</th>";
    echo "<th>Gesamtpunkte</th>";
    echo "</tr>";

    // SQL-Befehl ausführen und nach der Anzahl der richtigen Antworten absteigend sortieren
    $result = sqlbefehl($conn, "SELECT Gruppe, 
                                  SUM(CASE WHEN Korrektur = 'Ausstehend' THEN 1 ELSE 0 END) AS Ausstehend,
                                  SUM(CASE WHEN Korrektur = 'Richtig' THEN 1 ELSE 0 END) AS Richtig,
                                  SUM(CASE WHEN Korrektur = 'Falsch' THEN 1 ELSE 0 END) AS Falsch,
                                  SUM(Punkte) AS Gesamtpunkte
                                  FROM Antworten 
                                  GROUP BY Gruppe 
                                  ORDER BY Gesamtpunkte DESC");

    // Ergebnisse ausgeben
    if ($result->num_rows > 0) {
        $platz = 1;
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $platz . "</td>";
            echo "<td>" . $row["Gruppe"]. "</td>";
            echo "<td>" . $row["Ausstehend"]. "</td>";
            echo "<td>" . $row["Richtig"]. "</td>";
            echo "<td>" . $row["Falsch"]. "</td>";
            echo "<td>" . $row["Gesamtpunkte"]. "</td>";
            echo "</tr>";
            $platz++;
        }
    } else {
        echo "<tr><td colspan='6'>0 Ergebnisse gefunden</td></tr>";
    }

    echo "</table>";
} else {
    // Zeige die ursprüngliche Seite an
    echo "<h2>Stationen pro Gruppe</h2>";
    
    echo "<table>";
    echo "<tr>";
    echo "<th>Platz</th>";
    echo "<th>Gruppe</th>";
    echo "<th>Anzahl der bearbeiteten Stationen</th>";
    echo "</tr>";

    // SQL-Befehl ausführen und nach der Anzahl der bearbeiteten Stationen absteigend sortieren
    $result = sqlbefehl($conn, "SELECT Gruppe, 
                                  COUNT(DISTINCT Station) AS Anzahl_Stationen,
                                  SUM(Punkte) AS Gesamtpunkte
                                  FROM Antworten 
                                  GROUP BY Gruppe 
                                  ORDER BY Gesamtpunkte DESC");

    // Ergebnisse ausgeben
    if ($result->num_rows > 0) {
        $platz = 1;
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $platz . "</td>";
            echo "<td>" . $row["Gruppe"]. "</td>";
            echo "<td>" . $row["Anzahl_Stationen"]. "</td>";
            echo "</tr>";
            $platz++;
        }
    } else {
        echo "<tr><td colspan='3'>0 Ergebnisse gefunden</td></tr>";
    }

    echo "</table>";
}

// Verbindung schließen
$conn->close();
?>

</body>
</html>
