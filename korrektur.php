<?php
include('module.php');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KORREKTUR</title>
    <link rel="stylesheet" href="darksite.css"/>
    <link rel="stylesheet" href="korrektur.css"/>
</head>
<body>
    <table class="korrekturtable">
        <tr>
            <th>Gruppe</th>
            <th>Station</th>
            <th>Frage</th>
            <th>Antwort</th>
            <th>Musterlösungen</th>
            <th>Korrekturstatus</th>
            <th>maxPunkte</th>
            <th>Punkte</th>
            <th>Aktionen</th>
        </tr>
        <?php
        $conn = dbconnect();
        $sql = 'SELECT * FROM Antworten';
        foreach ($conn->query($sql) as $antwortentable) {
            $stationtable = $antwortentable["Station"];
            $idfrage = $antwortentable["IDFrage"];
            if(checktableexistence($conn, $stationtable)){
                $erg = sqlbefehl($conn, "SELECT * FROM $stationtable WHERE ID = $idfrage");
                if($erg->num_rows){
                    $stationtable = $erg -> fetch_assoc();          
                echo '<tr>';
                    echo ' <form action="editcorrect.php" method="get">';
                        echo '<th> '. $antwortentable["Gruppe"].'</th>';
                        echo '<th> '. $antwortentable["Station"].'</th>';
                        echo '<th> '. $stationtable["Frage"].'</th>';
                        echo '<th> '. $antwortentable["Antwort"].'</th>';
                        echo '<th> '. $stationtable["Antwort"].'</th>';
                        echo '<th> '. '
                            <label for="korrektur"></label>
                            <input type="text" id="korrektur" name="korrektur" value="'. $antwortentable["Korrektur"].'"></input></th>';
                        echo '<th> '. $stationtable["Punkte"].'</th>';
                        echo '<th> '. 
                        "<label for='punkte'></label>
                        <input type='number' id='punkte' name='punkte' value='". $antwortentable["Punkte"]."'></th>";


                        $id = $antwortentable["ID"];
                        echo '<th> '. "<a href='delete.php?id=$id&tabelle=Antworten&fileid=korrektur'> Löschen</a> <input type='submit' value='ändern'></input>". '</th>';

                        echo '<input type="hidden" id="id" name="id" value='. $antwortentable["ID"]. '></input>';

                    echo '</form>';
                echo '</tr>';
                }
            }else{
                echo '<tr>';
                    echo '<th> '. $antwortentable["Gruppe"].'</th>';
                    echo '<th> '. $antwortentable["Station"].'</th>';
                    echo '<th colspan="6">';
                        echo 'Hier befindet sich ein Datensatz zu einer nicht vorhandenden Station.... Wir empfehlen eine löschung!';
                    echo '</th>';
                    $id = $antwortentable["ID"];
                    echo '<th> '. "<a href='delete.php?id=$id&tabelle=Antworten&fileid=korrektur'> Löschen</a>". '</th>';
                echo '</tr>';
            }
        }
        ?>
    </table>
</body>
</html>



            

    



