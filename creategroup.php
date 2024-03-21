<?php
include('module.php');
$conn = dbconnect();
createtable($conn, "Gruppen");


if(isset($_POST['groupname'])){
    if($_POST['groupname']!="stations"){
        foreach ($_POST as $key => $value) {
            ${$key} = "'". $value. "'";
        }
        $passwordhash = "'". password_hash($_POST["password"], PASSWORD_DEFAULT). "'";
        $username = $_POST['groupname'];
        sqlbefehl($conn, "INSERT INTO `Gruppen` (`ID`, `Gruppenname`, `Passwort`) VALUES (NULL, $groupname, $passwordhash);");
    }else{
        echo("Der Gruppenname ist nicht erlaubt bitte w&auml;hlen einen anderen.");
    }
    aktualisiereIds($conn, "Gruppen");
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Gruppe erstellen </title>
    <link rel="stylesheet" href="darksite.css"/>
    <link rel="stylesheet" href="station.css"/>
</head>
<body>
    <div class="content">
        <div class="layout">

            <h1>Gruppe erstellen</h1>
            <hr></br>
            <form action="creategroup.php" method="post">
                <label for="groupname"> Gruppenname: </label>
	            <input id="groupname" name="groupname" placeholder="Katzen" value=""  type='text' required></input>
                </br></br></br>

                <label for="password">Gruppenpasswort:</label>
                <input id="password" name="password" type="password" required></input> 
                </br></br>

                <button style="font-size:20px;" id='erstellen' type="submit" value="Submit" submit-btn >Erstellen</button></br></br>
            </form>

        </div>


        <div class="stationentabelle">
            <?php 
                zeichneTabelle($conn, "Gruppen", "group");
            ?>
        </div>
    </div>
</body>
</html>
