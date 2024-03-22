<?php
include("module.php");
if (!empty($_GET)) {
    echo "&uuml;bertragene Daten über die GET-Methode:\n";
    foreach ($_GET as $key => $value) {
        echo "$key: $value\n";
    }
} else {
    echo "Keine &uuml;bertragenen Daten über die GET-Methode gefunden.";
}

$conn = dbconnect();
if(isset($_GET["id"])){
    $punkte = removesqlinjection($_GET["punkte"]);
    $id = removesqlinjection($_GET["id"]);
    $korrekturstate = removesqlinjection($_GET["korrektur"]);
    sqlbefehl($conn, "UPDATE Antworten SET Punkte = $punkte, korrektur = '$korrekturstate' WHERE ID = $id");
}
echo "
<script>
alert('Erfolgreich geändert!');
location.href = 'https://xn--kpenickralley-imb.de/korrektur.php';
</script>
";
?>