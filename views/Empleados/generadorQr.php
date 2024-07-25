<?php
session_start();

// Verifica si la sesión está iniciada y contiene el username
if (!isset($_SESSION['username'])) {
    die("Acceso no autorizado.");
}

// Obtener el documento del empleado de la variable de sesión
$documento_empleado = $_SESSION['username'];

// Generar el contenido inicial para el código QR utilizando el documento del empleado
$contenido_qr = $documento_empleado;

// Generar un timestamp para asegurar que el código QR sea único en cada solicitud
$timestamp = time();

// URL del servicio en línea para generar códigos QR
$qrCodeAPIBaseURL = 'https://api.qrserver.com/v1/create-qr-code/';
$qrCodeAPIURL = $qrCodeAPIBaseURL . '?size=300x300&data=' . urlencode($contenido_qr) . '&timestamp=' . $timestamp . '&rand=' . uniqid();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Código QR</title>
</head>
<body>
    <div class="container">
        <h2>Generador de Código QR</h2>
        <p>Documento del empleado: <?php echo htmlspecialchars($documento_empleado, ENT_QUOTES, 'UTF-8'); ?></p>
        <!-- Utiliza un elemento img para mostrar el código QR -->
        <img id="qrCodeImage" src="<?php echo htmlspecialchars($qrCodeAPIURL, ENT_QUOTES, 'UTF-8'); ?>" alt="Código QR" width="300" height="300">
        <br><br>
        <p id="counter">Actualizando en 10 segundos...</p> <!-- Contador de actualización -->
    </div>

    <script>
    // Función para actualizar el código QR y el contador
    function actualizarCodigoQR() {
        // Generar un nuevo timestamp para garantizar que el código QR sea único
        var timestamp = new Date().getTime();
        // Actualizar la URL del código QR con el nuevo timestamp
        var qrCodeURL = '<?php echo htmlspecialchars($qrCodeAPIBaseURL, ENT_QUOTES, 'UTF-8'); ?>?size=300x300&data=<?php echo urlencode($contenido_qr); ?>&timestamp=' + timestamp + '&rand=' + Math.random();
        // Obtener el elemento img que muestra el código QR
        var qrCodeImg = document.getElementById('qrCodeImage');
        // Actualizar la URL del código QR
        qrCodeImg.src = qrCodeURL;
    }

    // Función para actualizar el contador y el código QR
    function actualizarContador() {
        var secondsLeft = 10; // 10 segundos antes de la próxima actualización

        // Actualizar el contador cada segundo
        var interval = setInterval(function() {
            document.getElementById('counter').textContent = 'Actualizando en ' + secondsLeft + ' segundos...';
            secondsLeft--;

            if (secondsLeft < 0) {
                clearInterval(interval); // Detener el contador cuando alcance 0
                actualizarCodigoQR(); // Actualizar el código QR
                actualizarContador(); // Reiniciar el contador
            }
        }, 1000);
    }

    // Iniciar la actualización del contador
    actualizarContador();
</script>


</body>
</html>
