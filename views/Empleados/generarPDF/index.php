<?php
require_once('../../../config/db.php');

// Construir la ruta base utilizando la constante __DIR__
$base_url = rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/';

// Iniciar sesión
session_start();

// Resto del código...
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargar y Previsualizar PDF</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Descargar y Previsualizar PDF</h1>

    <form action="generar_pdf.php" method="post">
        <select id="pdfSelect" name="pdf">
            <option value="pdf1">PDF 1</option>
            <option value="pdf2">PDF 2</option>
            <!-- Agrega más opciones según necesites -->
        </select>
    </form>

    <!-- Botón para descargar PDF -->
    <a id="downloadBtn" href="#" style="display: none;">Descargar PDF</a>

    <!-- Contenedor para la previsualización del PDF -->
    <div id="pdfPreview">
        <!-- Utilizar la variable $base_url para cargar el PDF -->
        <embed id="pdfEmbed" src="<?php echo $base_url; ?>generar_pdf.php?pdf=pdf1" type="application/pdf" width="100%" height="600px" />
    </div>

    <script>
        // Manejador de evento para el cambio en el menú desplegable
        document.getElementById('pdfSelect').addEventListener('change', function() {
            // Obtener el valor del PDF seleccionado
            var pdfValue = this.value;

            // Establecer la ruta del PDF en el objeto embed
            document.getElementById('pdfEmbed').src = '<?php echo $base_url; ?>generar_pdf.php?pdf=' + pdfValue;

            // Mostrar el botón de descarga
            document.getElementById('downloadBtn').style.display = 'inline-block';
            // Actualizar el enlace de descarga
            document.getElementById('downloadBtn').href = '<?php echo $base_url; ?>generar_pdf.php?pdf=' + pdfValue;
            document.getElementById('downloadBtn').download = 'pdf_'+ pdfValue + '.pdf';
        });

        // Previsualizar el PDF al cargar la página
        document.getElementById('pdfSelect').dispatchEvent(new Event('change'));
    </script>
</body>
</html>
