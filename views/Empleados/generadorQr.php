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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
            margin: 0;
        }

        /* QR Container */
        .qr-container {
            background: #fff;
            width: 340px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            border: 2px solid #333;
            position: relative;
        }

        /* Header */
        .qr-header {
            background: #333;
            color: #fff;
            padding: 10px;
            border-radius: 12px 12px 0 0;
            font-size: 18px;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* QR Image */
        .qr-image-container {
            margin: 20px auto;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .qr-image {
            width: 260px;
            height: 260px;
            border-radius: 8px;
        }

        /* Employee Document Info */
        .employee-info {
            font-size: 15px;
            color: #555;
            margin: 15px 0;
            display: block;
        }
        .employee-info strong {
            color: #333;
            font-weight: 500;
        }

        /* Counter */
        .counter {
            font-size: 14px;
            color: #888;
            margin-top: 10px;
        }

        /* Responsive Design */
        @media (max-width: 400px) {
            .qr-container {
                width: 90%;
                padding: 15px;
            }
            .qr-header {
                font-size: 16px;
            }
            .qr-image-container {
                padding: 10px;
            }
            .qr-image {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <div class="qr-header">Generador de Código QR</div>
        <div class="employee-info">
            <strong>Documento:</strong> <span><?php echo htmlspecialchars($documento_empleado, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <!-- Display QR code in a padded container -->
        <div class="qr-image-container">
            <img id="qrCodeImage" class="qr-image" src="<?php echo htmlspecialchars($qrCodeAPIURL, ENT_QUOTES, 'UTF-8'); ?>" alt="Código QR">
        </div>
        <p class="counter" id="counter">Actualizando en 10 segundos...</p> <!-- Countdown timer -->
    </div>

    <script>
    // Function to update the QR code and counter
    function actualizarCodigoQR() {
        // Generate a new timestamp to ensure the QR code is unique
        var timestamp = new Date().getTime();
        // Update the QR code URL with the new timestamp
        var qrCodeURL = '<?php echo htmlspecialchars($qrCodeAPIBaseURL, ENT_QUOTES, 'UTF-8'); ?>?size=300x300&data=<?php echo urlencode($contenido_qr); ?>&timestamp=' + timestamp + '&rand=' + Math.random();
        // Get the img element that displays the QR code
        var qrCodeImg = document.getElementById('qrCodeImage');
        // Update the QR code URL
        qrCodeImg.src = qrCodeURL;
    }

    // Function to update the countdown and refresh the QR code
    function actualizarContador() {
        var secondsLeft = 10; // 10 seconds until the next update

        // Update the counter every second
        var interval = setInterval(function() {
            document.getElementById('counter').textContent = 'Actualizando en ' + secondsLeft + ' segundos...';
            secondsLeft--;

            if (secondsLeft < 0) {
                clearInterval(interval); // Stop the counter when it reaches 0
                actualizarCodigoQR(); // Refresh the QR code
                actualizarContador(); // Restart the countdown
            }
        }, 1000);
    }

    // Start the countdown on page load
    actualizarContador();
    </script>
</body>
</html>
