<?php
include('module.php');
$conn = dbconnect();

if(isset($_GET['stationid'])){
    $stationid = removesqlinjection($_GET['stationid']);
    createfragetable($conn, $stationid);
}else{
    $stationid = 0;
}

$tablename = getstationtablename($conn, $stationid);
if($tablename==false){
    echo "Diese Station existiert nicht wozu sie versuchen eine Frage zu erstellen.";
}else{
    if(isset($_POST['frage'])){
        foreach ($_POST as $key => $value) {
            ${$key} = "'". removesqlinjection($value). "'";
        }
        sqlbefehl($conn, "INSERT INTO `$tablename` (`ID`, `Frage`, `Antwort`, `Punkte`, `Fragentyp`) VALUES (NULL, $frage, $antwort, $punkte, $fragentyp);");
        aktualisiereIds($conn, $tablename);
    }
}

aktualisiereIds($conn, $tablename);
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    if($tablename!=false){
        echo "<title>Fragen hinzuf&uuml;gen - $tablename </title>";
    }else{
        echo "<title>Fragen hinzuf&uuml;gen - Fehler </title>";
    }
    ?>
    <link rel="stylesheet" href="darksite.css"/>
    <link rel="stylesheet" href="addfragen.css"/>
</head>
<body>
    <?php
        if($tablename!=false){
            echo '
                <div class="layout">
                    <h1>Fragen erstellen für die Station '. getstationname($conn, $stationid). '</h1>
                    <hr></br>
                    <form action="addfragen.php?stationid='. $stationid. '" method="post">
                        <label for="frage"> Frage: </label>
	                    <input id="frage" name="frage" placeholder="Wie alt wurde Emmy Noether?" value=""  type="text" required></input>
                        </br></br></br>
                        <label for="antwort"> Antwort: </label>
                        <input id="antwort" name="antwort" placeholder="53" value="" type="text" required></input> 
                        </br></br></br>
                        <label for="fragentyp"> Fragentyp</label>
                        <select name="fragentyp" id="fragentyp">
                            <option value="frage"> Frage </option>
                            <option value="multiplechoice"> multiplechoice </option>
                            <option value="bild"> Bildupload </option>
                        </select>
                        </br></br></br>
                        <label for="punkte"> Maximal zu erreichende Punkte: </label>
                        <input id="punkte" name="punkte" placeholder="1" value="1" type="int" required></input> 

                        </br></br>
                        <button style="font-size:20px;" id="erstellen" type="submit" value="Submit" submit-btn >Erstellen</button></br></br>
                    </form>
                    <a href="createstation.php"> Stations&uuml;bersicht</a>
                </div>';
            echo '<div class="fragentabelle">';
            zeichneTabelle($conn, $tablename, "addfrage");
            echo '</div>';
        }
    ?>
</body>
</html>

