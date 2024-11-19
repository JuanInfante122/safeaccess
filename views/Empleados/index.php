<?php
require_once('../../config/db.php');

// Iniciar sesión
session_start();

// Verificar si la variable de sesión está definida y no está vacía
if (isset($_SESSION["username"]) && !empty($_SESSION["username"])) {
    // Obtener el nombre de usuario de la sesión
    $username = $_SESSION["username"];

    try {
        // Conectar a la base de datos
        $db = Database::connect();

        // Preparar la consulta SQL
        $query = "SELECT * FROM tbl_empleado WHERE emp_documento = ?";
        $stmt = $db->prepare($query);

        // Vincular parámetros y ejecutar la consulta
        $stmt->bind_param("s", $username);
        $stmt->execute();

        // Obtener el resultado de la consulta
        $result = $stmt->get_result();

        // Verificar si se encontraron registros
        if ($result->num_rows === 0) {
            // No se encontraron registros para la clave dada
            $cargo = "Cargo no encontrado";
            $usuario = "Usuario no encontrado";
        } else {
            // Obtener el nombre y el cargo del resultado de la consulta
            $row = $result->fetch_assoc();
            $id_usuario = $row['emp_documento'];
            $usuario = $row['emp_nombre'] . ' ' . $row['emp_apellidos'];
            $cargo = $row['emp_cargo'];
        }

        // Cerrar la conexión a la base de datos
        $stmt->close();
        $db->close();
    } catch (Exception $e) {
        $usuario = "Error al conectar a la base de datos: " . $e->getMessage();
        $cargo = "";
    }
} else {
    // La variable de sesión no está definida o está vacía
    $usuario = "Usuario no encontrado";
    $cargo = "";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Principal - Empleados</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body, html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
            color: #333;
            background-color: #f9f9f9;
            overflow: hidden;
        }
        .main-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: #333;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 20px;
            transition: width 0.3s;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
        }
        .sidebar h2 {
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #ecf0f1;
        }
        .sidebar a {
            color: #bdc3c7;
            text-decoration: none;
            padding: 12px 10px;
            margin-bottom: 8px;
            border-radius: 5px;
            transition: background 0.3s, color 0.3s;
            display: flex;
            align-items: center;
        }
        .sidebar a:hover {
            background: #555;
            color: #ecf0f1;
        }
        .sidebar i {
            margin-right: 10px;
            font-size: 16px;
        }

        /* Collapsed Sidebar */
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar.collapsed h2 {
            display: none;
        }
        .sidebar.collapsed a {
            justify-content: center;
        }
        .sidebar.collapsed a span {
            display: none;
        }

        /* Header Styles */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #333;
            color: #fff;
            padding: 15px 20px;
            width: calc(100% - 250px);
            position: fixed;
            top: 0;
            left: 250px;
            transition: width 0.3s, left 0.3s;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .header.collapsed {
            left: 70px;
            width: calc(100% - 70px);
        }
        .toggle-btn {
            background: #555;
            color: #fff;
            border: none;
            padding: 8px;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .toggle-btn:hover {
            background: #777;
        }

        /* Content Styles */
        .content {
            flex: 1;
            margin-top: 80px;
            padding: 20px;
            background: #fff;
            width: calc(100% - 250px);
            overflow-y: auto;
            transition: width 0.3s, margin-left 0.3s;
            margin-left: 250px;
        }
        .content.collapsed {
            width: calc(100% - 70px);
            margin-left: 70px;
        }
        #content {
            padding: 30px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 500px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }
        .welcome-message {
            font-size: 26px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        .welcome-description {
            font-size: 16px;
            color: #555;
            max-width: 500px;
            line-height: 1.6;
            text-align: center;
            margin: 0 auto;
        }
        .welcome-image {
            width: 150px;
            height: 150px;
            margin-top: 20px;
            border-radius: 50%;
            background-color: #ddd;
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Footer Styles */
        footer {
            text-align: center;
            padding: 10px;
            font-size: 14px;
            background: #333;
            color: #fff;
            position: fixed;
            bottom: 0;
            width: calc(100% - 250px);
            left: 250px;
        }
        .footer.collapsed {
            width: calc(100% - 70px);
            left: 70px;
        }

        /* Media Queries */
        @media (max-width: 600px) {
            .header {
                width: calc(100% - 70px);
                left: 70px;
            }
            .content {
                width: calc(100% - 70px);
                margin-left: 70px;
            }
            .footer {
                width: calc(100% - 70px);
                left: 70px;
            }
            .sidebar {
                width: 70px;
            }
            .sidebar h2, .sidebar a span {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <div>
            <span>Safe Access</span>
            <h6 class="titulo"><?php echo htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8'); ?></h6>
        </div>
    </div>
    <div class="main-container">
        <div class="sidebar" id="sidebar">
            <h2>Safe Access</h2>
            <a href="#" onclick="loadContent('perfil.php')"><i class="fas fa-user"></i><span>Ver perfil</span></a>
            <a href="#" onclick="loadContent('generadorQr.php');"><i class="fas fa-qrcode"></i><span>Código QR</span></a>
            <a href="#" onclick="loadContent('accesos.php')"><i class="fas fa-key"></i><span>Accesos</span></a>
            <a href="#" onclick="loadContent('generarPDF/index.php');"><i class="fas fa-chart-line"></i><span>Reportes</span></a>
            <a href="#" onclick="loadContent('calendario.php')"><i class="fas fa-calendar-alt"></i><span>Calendario</span></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Cerrar sesión</span></a>
        </div>
        <div class="content" id="content">
            <div class="welcome-message">¡Bienvenido a Safe Access!</div>
            <p class="welcome-description">Selecciona una opción del menú a la izquierda para comenzar a explorar el sistema y gestionar el acceso seguro de empleados.</p>
            <div class="welcome-image">150 x 150</div>
        </div>
    </div>

    <footer class="footer">
        &copy; 2024 Safe Access. Todos los derechos reservados.
    </footer>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.content');
            const header = document.querySelector('.header');
            const footer = document.querySelector('.footer');
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
            header.classList.toggle('collapsed');
            footer.classList.toggle('collapsed');
        }

        function changeContent(option) {
            const contentDiv = document.getElementById('content');
            let content = '';
            switch(option) {
                case 'perfil':
                    content = `<h1>Perfil</h1><p>Contenido del perfil aquí.</p>`;
                    break;
                case 'qr':
                    content = `<h1>Código QR</h1><p>Contenido del código QR aquí.</p>`;
                    break;
                case 'accesos':
                    content = `<h1>Accesos</h1><p>Contenido de accesos aquí.</p>`;
                    break;
                case 'pdf':
                    content = `<h1>Reportes</h1><p>Contenido de reportes aquí.</p>`;
                    break;
                case 'calendario':
                    content = `<h1>Calendario</h1><p>Contenido del calendario aquí.</p>`;
                    break;
                case 'cerrar-sesion':
                    content = `<h1>Cerrar Sesión</h1><p>Funcionalidad de cerrar sesión aquí.</p>`;
                    break;
                default:
                    content = `<h1>Bienvenido</h1><p>Selecciona una opción del menú.</p>`;
                    break;
            }
            contentDiv.innerHTML = content;
        }

        function loadContent(url) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("content").innerHTML = this.responseText;
                    executeScripts();
                } else if (this.readyState == 4) {
                    document.getElementById("content").innerHTML = `<h1>Error</h1><p>No se pudo cargar el contenido.</p>`;
                }
            };
            xhttp.open("GET", url, true);
            xhttp.send();
        }

        function executeScripts() {
            const scripts = document.querySelectorAll('#content script');
            scripts.forEach((script) => {
                const newScript = document.createElement('script');
                newScript.textContent = script.textContent;
                document.body.appendChild(newScript).parentNode.removeChild(newScript);
            });
        }
    </script>
</body>
</html>
