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
        }

        .cabeza img {
            height: 40px;
            margin-right: 10px;
        }

        .cabeza h1 {
            margin: 0;
            font-size: 1.2em;
        }

        .areas, .escaneo {
            padding: 20px;
        }

        .areas {
            display: block;
        }

        .escaneo {
            display: none;
        }

        .escan {
            background-color: #138d75;
            padding: 10px;
            color: white;
        }

        #preview {
            display: none;
            width: 100%;
        }

        .background-animation {
            position: fixed;
            top: 500px;
            left: 50px;
            width: 80%;
            height: 50%;
            animation: scanAnimation 4s linear infinite;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        @keyframes scanAnimation {
            0% { background-position: -100% 50%; }
            100% { background-position: 200% 50%; }
        }

        .back-button {
            background-color: #f4f4f4;
            padding: 10px;
            cursor: pointer;
            text-align: center;
            border-radius: 5px;
            margin-top: 10px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <div class="cabeza">
        <img src='../assets/Logo-Sena.jpg' alt='logo'>
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

    <div class="escaneo" id="escaneo">
        <h1 class="escan">Escaneando</h1>
        <video id="preview"></video>
        <div class="background-animation"></div>
        <button onclick="scanQR()">Escanear con cámara</button>
        <button class="back-button" onclick="mostrarAreas()">Volver a Seleccionar Área</button>
        <div id="employeeInfo" style="display:none;">
            <h2>Información del Empleado</h2>
            <p><strong>Nombre:</strong> <span id="employeeName"></span></p>
            <p><strong>Apellidos:</strong> <span id="employeeLastname"></span></p>
        </div>
        <form id="imageForm" action="" method="post" enctype="multipart/form-data" style="display:none;">
            <input type="file" accept="image/*" name="archivo" id="fileInput">
            <button type="submit" name="submit">Leer QR desde imagen</button>
        </form>
        <canvas id="canvas" style="display:none;"></canvas>
        <div id="fecha-hora"></div>
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
                    if (data.accessGranted) {
                        // Si se concede el acceso
                        // Si se concede el acceso, obtiene y muestra la información del empleado
                        fetch('get_employee_info.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ qrContent: content })
                        })
                        .then(response => response.json())
                        .then(employeeData => {
                            // Muestra la información del empleado
                            document.getElementById('employeeName').textContent = employeeData.nombre;
                            document.getElementById('employeeLastname').textContent = employeeData.apellidos;
                            document.getElementById('employeeInfo').style.display = 'block';
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al obtener la información del empleado.');
                        });
                        alert('Acceso concedido.');
                    } else {
                        alert('Acceso denegado.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
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
