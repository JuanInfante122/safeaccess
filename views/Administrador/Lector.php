<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ambientes de Formación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .cabeza {
            background-color: white;
            color: #000;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            box-sizing: border-box;
        }

        .cabeza img {
            height: 40px;
            margin-right: 10px;
        }

        .cabeza h1 {
            margin: 0;
            font-size: 1.2em;
        }

        .areas, .button-container {
            padding: 20px;
            display: none; /* Ocultamos por defecto */
            box-sizing: border-box;
        }

        .areas {
            display: block; /* Mostrar por defecto */
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            background-color: #ffffff;
            border-bottom: 2px solid #ddd;
        }

        .button-container button {
            background-color: #138d75;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button-container button:hover {
            background-color: #117a65;
        }

        .button-container #fecha-hora {
            font-size: 1em;
            color: #333;
            margin: 0;
        }

        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            border: 2px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            z-index: 1000;
            max-width: 90%;
            width: 300px;
            text-align: center;
            transition: border-color 0.3s ease, opacity 0.3s ease;
        }

        .popup.success {
            border-color: green;
        }

        .popup.error {
            border-color: red;
        }


        .popup h2 {
            margin: 0 0 10px;
        }

        .popup p {
            margin: 5px 0;
        }

        .popup button {
            background-color: #138d75;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .popup button:hover {
            background-color: #117a65;
        }

        @media (max-width: 768px) {
            .cabeza {
                flex-direction: column;
                text-align: center;
            }

            .cabeza img {
                margin: 0 auto 10px;
            }

            .cabeza h1 {
                font-size: 1.5em;
            }

            .areas, .button-container {
                padding: 10px;
            }

            .button-container {
                flex-direction: column;
                align-items: center;
            }

            .button-container button {
                margin: 5px 0;
            }
        }

        @media (max-width: 480px) {
            .cabeza h1 {
                font-size: 1.2em;
            }

            .button-container {
                padding: 5px;
            }

            .button-container button {
                padding: 8px 16px;
                font-size: 0.9em;
            }
        }

        .escaneo {
            display: none;
            text-align: center;
        }

        .escaneo video {
            width: 100%;
            max-width: 600px;
            height: auto;
            border: 2px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .background-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1);
            z-index: -1;
            animation: backgroundAnimation 10s linear infinite;
        }

        @keyframes backgroundAnimation {
            0% { background-color: rgba(0, 0, 0, 0.1); }
            50% { background-color: rgba(0, 0, 0, 0.3); }
            100% { background-color: rgba(0, 0, 0, 0.1); }
        }

        .escan {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .escaneo {
                padding: 10px;
            }

            .escaneo video {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="cabeza">
        <h1>Gestión de Ambientes de Formación</h1>
    </div>

    <div class="areas" id="areas">
        <h2>Selecciona un Área</h2>
        <select id="selectArea">
            <option value="">Seleccionar Área</option>
            <!-- PHP para mostrar las opciones de área -->
            <?php
            // Conexión a la base de datos
            $servername = "localhost"; // Cambia esto según sea necesario
            $username = "root"; // Cambia esto según sea necesario
            $password = ""; // Cambia esto según sea necesario
            $dbname = "safeacces"; // Cambia esto según sea necesario

            // Crear conexión
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Verificar conexión
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Obtener áreas de la base de datos
            $sql = "SELECT area_id, area_nombre FROM tbl_area";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['area_id'] . '">' . $row['area_nombre'] . '</option>';
                }
            } else {
                echo "<option value=''>No hay áreas disponibles</option>";
            }
            $conn->close();
            ?>
        </select>
        <button onclick="seleccionarArea()">Seleccionar</button>
    </div>

    <div class="button-container" id="button-container">
        <button class="back-button" onclick="mostrarAreas()">Volver a Seleccionar Área</button>
        <!-- El botón para escanear con cámara ha sido eliminado -->
        <div id="fecha-hora"></div>
    </div>

    <div class="escaneo" id="escaneo">
        <h1 class="escan">Escaneando</h1>
        <video id="preview"></video>
        <div class="background-animation"></div>
        <form id="imageForm" action="" method="post" enctype="multipart/form-data" style="display:none;">
            <input type="file" accept="image/*" name="archivo" id="fileInput">
            <button type="submit" name="submit">Leer QR desde imagen</button>
        </form>
        <canvas id="canvas" style="display:none;"></canvas>
    </div>

    <!-- Popup de información del empleado -->
    <div id="employeePopup" class="popup">
        <h2>Información del Empleado</h2>
        <p><strong>Nombre:</strong> <span id="employeeName"></span></p>
        <p><strong>Apellidos:</strong> <span id="employeeLastname"></span></p>
        <button onclick="closePopup()">Cerrar</button>
    </div>

    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script>
        let scanner;
        let areaId;

        function seleccionarArea() {
            const select = document.getElementById('selectArea');
            areaId = select.value;
            if (areaId) {
                document.getElementById('areas').style.display = 'none';
                document.getElementById('escaneo').style.display = 'block';
                scanQR(); // Inicia el escaneo automáticamente
            } else {
                alert('Por favor, selecciona un área.');
            }
        }

        function mostrarAreas() {
            document.getElementById('areas').style.display = 'block';
            document.getElementById('escaneo').style.display = 'none';
            if (scanner) {
                scanner.stop();
            }
        }

        function scanQR() {
            if (!areaId) {
                alert('ID de área no proporcionado.');
                return;
            }

            document.getElementById('preview').style.display = 'block';
            document.getElementById('imageForm').style.display = 'none';

            scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
            scanner.addListener('scan', function (content) {
                // Reproducir sonido de bip
                const beep = new Audio('beep.mp3'); // Asegúrate de tener un archivo beep.mp3 en la raíz
                beep.play();

                // Realizar una solicitud al servidor para validar el acceso del empleado al área
                fetch('validate_acces.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ qrContent: content, areaId: areaId })
                })
                .then(response => response.json())
                .then(data => {
                    // Obtener el popup y el sonido
                    const popup = document.getElementById('employeePopup');
                    const employeeName = document.getElementById('employeeName');
                    const employeeLastname = document.getElementById('employeeLastname');

                    if (data.accessGranted) {
                        // Si se concede el acceso, muestra la información del empleado
                        fetch('get_employee_info.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ qrContent: content })
                        })
                        .then(response => response.json())
                        .then(employeeData => {
                            employeeName.textContent = employeeData.nombre;
                            employeeLastname.textContent = employeeData.apellidos;
                            popup.classList.add('success');
                            popup.style.display = 'block';
                        })
                        .catch(error => {
                            console.error('Error al obtener la información del empleado:', error);
                            popup.classList.add('error');
                            popup.style.display = 'block';
                            document.getElementById('employeeName').textContent = 'Error';
                            document.getElementById('employeeLastname').textContent = 'Error';
                        });
                    } else {
                        popup.classList.add('error');
                        popup.style.display = 'block';
                        employeeName.textContent = 'Acceso Denegado';
                        employeeLastname.textContent = '';
                    }
                })
                .catch(error => {
                    console.error('Error al procesar el acceso:', error);
                    alert('Error al procesar el acceso.');
                });
            });

            Instascan.Camera.getCameras().then(function (cameras) {
                if (cameras.length > 0) {
                    let rearCamera = cameras.find(camera => camera.name.toLowerCase().includes('back'));
                    if (rearCamera) {
                        scanner.start(rearCamera);
                    } else {
                        scanner.start(cameras[0]);
                    }
                } else {
                    console.error('No se encontraron cámaras disponibles.');
                    alert('No se encontraron cámaras disponibles.');
                }
            }).catch(function (e) {
                console.error('Error al acceder a las cámaras:', e);
                alert('Error al acceder a las cámaras. Asegúrate de que tienes permiso para acceder a la cámara y de que estás utilizando un dispositivo compatible.');
            });
        }

        function closePopup() {
            const popup = document.getElementById('employeePopup');
            popup.style.display = 'none';
            popup.classList.remove('success', 'error');
        }

        function obtenerFechaHora() {
            let fechaHora = new Date();
            let fecha = fechaHora.toLocaleDateString();
            let hora = fechaHora.toLocaleTimeString();
            document.getElementById('fecha-hora').innerText = `Fecha: ${fecha}, Hora: ${hora}`;
        }

        obtenerFechaHora();
        setInterval(obtenerFechaHora, 1000);
    </script>
</body>
</html>

