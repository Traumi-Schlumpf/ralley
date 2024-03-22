<?php
include('module.php');
$conn = dbconnect();

if(!angemeldet($conn)){
    loginmaske($conn);
}

if(!isset($_GET['stationname'])){
    header('https://xn--kpenickralley-imb.de');
    echo '
        <script>
            location.href = "https://xn--kpenickralley-imb.de";
        </script>
    ';
}


if(isset($_GET['fragenid'])){
    $fragenid = $_GET['fragenid'];
}else{
    $fragenid = 1;
}

$stationname = removesqlinjection($_GET['stationname']);

$stationid = sqlbefehl($conn, "SELECT * FROM stations WHERE Name = '$stationname'");
$stationid = $stationid->fetch_assoc()["ID"];
if(checktableexistence($conn, removeleerzeichen($stationname))){
    $frage = sqlbefehl($conn, "SELECT * FROM ". removeleerzeichen($stationname). " WHERE ID = $fragenid");
    $frage = $frage->fetch_assoc()["Frage"];
}else{
    $frage = "ERROR #404 NOT FOUND";
}
if(isset($_GET['antwort'])){
    if(checktableexistence($conn, removeleerzeichen($stationname))){
        if(angemeldet($conn)){
            createtable($conn, "Antworten");
            $groupname = $_SESSION['gruppenname'];
            $antwort = removesqlinjection($_GET['antwort']);
            $fragenid=$fragenid-1;

            //sqlbefehl($conn, "INSERT INTO `Antworten` (`ID`, `Gruppe`, `Station`, `IDFrage`, `Antwort`, `Korrektur`, `Punkte`) Values (NULL, '$groupname', '$stationname', $fragenid-1, '$antwort', 'Ausstehend', 0);");
            

            $sql = "SELECT COUNT(*) AS count FROM Antworten WHERE Gruppe = '$groupname' AND Station = '$stationname' AND IDFrage = $fragenid";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];

            if ($count > 0) {
                // Eintrag aktualisieren
                $updateSql = "UPDATE Antworten SET Antwort = '$antwort', Korrektur = 'Ausstehend', Punkte = -1 WHERE Gruppe = '$groupname' AND Station = '$stationname' AND IDFrage = $fragenid";
                mysqli_query($conn, $updateSql);
            } else {
                // Neuen Eintrag hinzufügen
                $insertSql = "INSERT INTO Antworten (Gruppe, Station, IDFrage, Antwort, Korrektur, Punkte) VALUES ('$groupname', '$stationname', $fragenid, '$antwort', 'Ausstehend', -1)";
                mysqli_query($conn, $insertSql);
            }
            aktualisiereIds($conn, "Antworten");
            $fragenid=$fragenid+1;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Station | <?php echo removesqlinjection($_GET['stationname']); ?> </title>
    <link rel="stylesheet" href="darksite.css"/>
    <link rel="stylesheet" href="station.css"/>
</head>
<body>
    <?php
        if(angemeldet($conn)){
            echo '
                <div class="progressbar">';
                    echo '<a class="home leftbar" href="https://köpenickralley.de">&#x2302; Home</a>';
                    $fragenid=$fragenid+1;
                    if(checktableexistence($conn, removeleerzeichen($stationname))){
                        $result = sqlbefehl($conn, "SELECT ID FROM ". removeleerzeichen($stationname));
                        if ($result->num_rows > 0) {
                            $rows = $result->fetch_all(MYSQLI_ASSOC);
                            foreach ($rows as $index => $row) {
                                $entry_id = $row['ID'];
                                $class = ($index === 0) ? 'middlebar' : (($index === count($rows) - 1) ? 'rightbar' : 'middlebar');
                                
                                if($entry_id==$fragenid-1){
                                    echo '<a class="'. $class.' skip" href="'."https://xn--kpenickralley-imb.de/station.php?stationname=$stationname&fragenid=$fragenid".'">&#9193;</a>';
                                }
                                elseif(hasfinishedquestion($conn, $groupname, $entry_id, $stationname)){
                                    echo "<a class='$class finished 'href='https://xn--kpenickralley-imb.de/station.php?stationname=$stationname&fragenid=$entry_id'>$entry_id </a>";
                                }else{
                                    echo "<a class='$class pending'href='https://xn--kpenickralley-imb.de/station.php?stationname=$stationname&fragenid=$entry_id'>$entry_id </a>";
                                }
                            
                            }
                        } else {
                            echo "bums.";
                        }
                    }
                    $fragenid=$fragenid-1;
                    //<div class="skip middlebar">&#9193;</div>
            echo '
                </div>
                <div class="frage">';
                if($fragenid <= $entry_id){
                    echo $frage;

                    echo '
                    <br style="font-size: 0.1vh;">
                    </div>
                    <div class="antwort">
                        <label for="antwort"></label>
                        <form action="station.php" method="get">
                            <input type="hidden" name="stationname" id="stationname" value="'. removesqlinjection($_GET['stationname']). '"></input>
                            <input type="hidden" name="fragenid" id="fragenid" value="'. ($fragenid+1).'"></input>
                            <input type="text" id="antwort" name="antwort" placeholder="Antwort"><button type="submit">&#8594;</button></input>
                        </form>
                    </div>
                ';
                }else{
                    echo 'beendet </div>';
                    echo '
                    <script type="text/javascript">
                    setTimeout(function() {
                        window.location.href = "https://xn--kpenickralley-imb.de";
                    }, 3000);
                    </script>
                    
                    ';
                }
            
        }
    ?>
</body>
</html>
