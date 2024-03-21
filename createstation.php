<?php
include('module.php');
$conn = dbconnect();
createtable($conn, "stations");
aktualisiereIds($conn, "stations");

if(isset($_POST['stationname'])){
    if($_POST['stationname']!="stations"){
        foreach ($_POST as $key => $value) {
            ${$key} = "'". removesqlinjection($value). "'";
        }
        sqlbefehl($conn, "INSERT INTO `stations` (`ID`, `Name`, `N-Koordinate`, `O-Koordinate`) VALUES (NULL, $stationname, $ncord, $ocord);");
    }else{
        echo("Der Stationsname ist nicht erlaubt bitte wählen einen anderen.");
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Station erstellen</title>
    <link rel="stylesheet" href="darksite.css"/>
    <link rel="stylesheet" href="station.css"/>
</head>
<body>
    <div class="content">
        <div class="layout">

            <h1>Station erstellen</h1>
            <hr></br>
            <form action="createstation.php" method="post">
                <label for="stationname"> Stationsname: </label>
	            <input id="stationname" name="stationname" placeholder="Station 1" value=""  type='text' required></input>
                </br></br></br>

                <label for="ncord">N-Koordinate:</label>
                <input id="ncord" name="ncord" step="0.001"  placeholder=52.443 required></input> 

                <label for="ocord">O-Koordinate:</label>
                <input id="ocord" name="ocord" step="0.001"  placeholder=13.588 required></input> 
                
                <p class="hinweis">HINWEIS: Die Koordinaten stehen im Dezimalgradformat(WGS-84) und werden per Punkt getrennt(siehe Beispiel)</p>
                </br></br>

                <button style="font-size:20px;" id='erstellen' type="submit" value="Submit" submit-btn >Erstellen</button></br></br>
            </form>

        </div>


        <div class="stationentabelle">
            <?php 
                zeichneTabelle($conn, "stations", "stations");
            ?>
        </div>
    </div>
</body>
</html>
