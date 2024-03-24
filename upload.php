<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dateiupload</title>
</head>
<body>

<?php
$zielverzeichnis = "uploads/"; // Das Verzeichnis, in dem die Datei gespeichert werden soll

if(isset($_POST["submit"])) {
    $dateiname = basename($_FILES["datei"]["name"]);
    $ziel = $zielverzeichnis . $dateiname;
    
    // Überprüfen, ob die Datei erfolgreich hochgeladen wurde
    if(move_uploaded_file($_FILES["datei"]["tmp_name"], $ziel)) {
        echo "Die Datei wurde erfolgreich hochgeladen und gespeichert.";
    } else{
        echo "Beim Hochladen der Datei ist ein Fehler aufgetreten.";
    }
}
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    <input type="file" name="datei" id="datei">
    <button type="submit" name="submit">Hochladen</button>
</form>

</body>
</html>
