<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Zum Zeichnen auffordern</title>
<style>
    canvas {
        border: 1px solid #000;
        cursor: crosshair;
    }
</style>
</head>
<body>
<canvas id="myCanvas" width="400" height="400"></canvas>
<button id="sendButton">Senden</button>

<script>
    // Canvas-Element und Kontext holen
    var canvas = document.getElementById('myCanvas');
    var ctx = canvas.getContext('2d');
    var isDrawing = false;

    // Zeige eine Aufforderung zum Zeichnen an
    ctx.font = '20px Arial';
    ctx.fillText('Bitte zeichnen Sie etwas auf dem Canvas.', 50, 50);

    // Funktion zum Zeichnen aufrufen, wenn die Maus bewegt wird
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);

    // Zeichenfunktion
    function draw(e) {
        if (!isDrawing) return;
        var x = e.clientX - canvas.getBoundingClientRect().left;
        var y = e.clientY - canvas.getBoundingClientRect().top;

        ctx.lineTo(x, y);
        ctx.stroke();
    }

    function startDrawing(e) {
        isDrawing = true;
        var x = e.clientX - canvas.getBoundingClientRect().left;
        var y = e.clientY - canvas.getBoundingClientRect().top;

        ctx.beginPath();
        ctx.moveTo(x, y);
    }

    function stopDrawing() {
        isDrawing = false;
    }

    // Funktion zum Senden des gezeichneten Bildes
    function sendDrawingToServer() {
        var imageData = canvas.toDataURL(); // Bild als Base64-Daten-URL erhalten
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    alert('Bild erfolgreich gesendet!');
                    // Optional: Hier können Sie eine Aktion ausführen, wenn das Bild erfolgreich gesendet wurde
                } else {
                    alert('Fehler beim Senden des Bildes.');
                    // Optional: Hier können Sie eine Aktion ausführen, wenn ein Fehler beim Senden des Bildes auftritt
                }
            }
        };
        xhr.open('POST', 'canvatest.php'); // URL zum Hochladen anpassen
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('image=' + encodeURIComponent(imageData)); // Bild an Server senden
    }

    // Event-Listener für den Senden-Button
    document.getElementById('sendButton').addEventListener('click', sendDrawingToServer);

</script>

<?php
// Überprüfen, ob ein Bild gesendet wurde
if (isset($_POST['image']) && !empty($_POST['image'])) {
    // Decode the base64 encoded image
    $imageData = $_POST['image'];
    $filteredData = substr($imageData, strpos($imageData, ",") + 1);
    $decodedData = base64_decode($filteredData);

    // Bild im Ordner speichern
    $filePath = 'uebermittelteBilder/canvas_image.png'; // Anpassen des Dateipfads
    file_put_contents($filePath, $decodedData);

    // Erfolgsmeldung anzeigen
    echo "<p>Bild erfolgreich empfangen und gespeichert unter: $filePath</p>";
}
?>

</body>
</html>
