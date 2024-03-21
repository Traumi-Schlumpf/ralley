<?php
include('module.php');
$conn = dbconnect();
$id = removesqlinjection($_GET['id']);
$tabelle = removesqlinjection($_GET['tabelle']);
if ($tabelle == "stations"){
    $fragentable = getstationtablename($conn, $id);
    if($fragentable!=false){
        if(checktableexistence($conn, $fragentable)){
            sqlbefehl($conn, "DROP TABLE $fragentable");
        }
    }
}
sqlbefehl($conn, "DELETE FROM $tabelle WHERE ID=$id");
aktualisiereIds($conn, $tabelle);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Station löschen</title>
    <link rel="stylesheet" href="darksite.css"/>
    <script>
        <?php
        if($_GET['fileid'] == "stations"){
            echo "window.location.href = 'https://xn--kpenickralley-imb.de/createstation.php';";
        }
        if($_GET['fileid'] == "group"){
            echo "window.location.href = 'https://xn--kpenickralley-imb.de/creategroup.php';";
        }
        if($_GET['fileid'] == "addfrage"){
            echo "window.location.href = 'https://xn--kpenickralley-imb.de/createstation.php';";
        }

        if($_GET['fileid'] == "korrektur"){
            echo "window.location.href = 'https://xn--kpenickralley-imb.de/korrektur.php';";
        }
        ?>
    </script>
</head>
<body>
    Eintrag wurde Gelöscht
</body>
</html>