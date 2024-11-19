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
            min-height: 100vh;
            color: #333;
            padding: 20px;
        }

        /* Main Container */
        .reports-container {
            background: #fff;
            width: 100%;
            max-width: 700px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            border: 2px solid #333;
        }

        /* Header */
        .reports-header {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        /* PDF Selector */
        .pdf-select-container {
            margin-bottom: 20px;
        }
        .pdf-select {
            padding: 10px;
            font-size: 16px;
            font-weight: 500;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #333;
            color: #fff;
            cursor: pointer;
            outline: none;
            transition: background 0.3s;
        }
        .pdf-select:hover {
            background-color: #555;
        }

        /* Download Button */
        .download-btn {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
            margin-top: 10px;
            transition: background 0.3s;
        }
        .download-btn:hover {
            background-color: #555;
        }

        /* PDF Viewer */
        .pdf-preview {
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            height: 600px;
            overflow: hidden;
        }

        /* Responsive Design */
        @media (max-width: 400px) {
            .reports-container {
                width: 90%;
                padding: 15px;
            }
            .reports-header {
                font-size: 18px;
            }
            .pdf-select {
                font-size: 14px;
                padding: 8px;
            }
            .download-btn {
                font-size: 14px;
                padding: 8px 16px;
            }
            .pdf-preview {
                height: 400px;
            }
        }
    </style>
</head>
<body>
    <div class="reports-container">
        <h1 class="reports-header">Descargar y Previsualizar PDF</h1>

        <!-- PDF Selector -->
        <form action="generar_pdf.php" method="post" class="pdf-select-container">
            <select id="pdfSelect" name="pdf" class="pdf-select">
                <option value="pdf1">Registro de Horas Trabajadas</option>
                <option value="pdf2">Certificado Laboral</option>
                <!-- Agrega más opciones según necesites -->
            </select>
        </form>

        <!-- Download Button -->
        <a id="downloadBtn" class="download-btn" href="#" style="display: none;">Descargar PDF</a>

        <!-- PDF Viewer -->
        <div id="pdfPreview" class="pdf-preview">
            <embed id="pdfEmbed" src="<?php echo $base_url; ?>generar_pdf.php?pdf=pdf1" type="application/pdf" width="100%" height="100%" />
        </div>
    </div>

    <script>
        // Event handler for PDF selection change
        document.getElementById('pdfSelect').addEventListener('change', function() {
            // Get selected PDF value
            var pdfValue = this.value;

            // Update PDF embed source
            document.getElementById('pdfEmbed').src = '<?php echo $base_url; ?>generar_pdf.php?pdf=' + pdfValue;

            // Display download button and update its link
            document.getElementById('downloadBtn').style.display = 'inline-block';
            document.getElementById('downloadBtn').href = '<?php echo $base_url; ?>generar_pdf.php?pdf=' + pdfValue;
            document.getElementById('downloadBtn').download = 'reporte_' + pdfValue + '.pdf';
        });

        // Trigger the PDF preview on page load
        document.getElementById('pdfSelect').dispatchEvent(new Event('change'));
    </script>
</body>
</html>
