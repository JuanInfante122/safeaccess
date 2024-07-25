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
    <title>Vista Principal - Administradores</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/instascan/1.0.0/instascan.min.js"></script>

    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #fff, #f1f1f1);
            color: #333;
            overflow-x: hidden;
        }
        .main-container {
            display: flex;
            height: 100%;
            width: 100%;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #e6e6e6, #f1f1f1);
            color: #333;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: width 0.3s, padding 0.3s;
            overflow: hidden;
        }
        .sidebar.collapsed {
            width: 80px;
            align-items: center;
            padding: 20px 10px;
        }
        .sidebar h2 {
            margin-bottom: 40px;
            font-size: 24px;
            text-align: center;
            transition: opacity 0.3s;
        }
        .sidebar.collapsed h2 {
            opacity: 0;
        }
        .sidebar a {
            color: #333;
            text-decoration: none;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background 0.3s, padding 0.3s;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
        }
        .sidebar.collapsed a {
            justify-content: center;
            padding: 10px 0;
        }
        .sidebar a:hover {
            background: #ddd;
        }
        .sidebar i {
            margin-right: 10px;
            font-size: 10px;
            transition: margin-right 0.3s, font-size 0.3s;
        }
        .sidebar.collapsed i {
            margin-right: 0;
            font-size: 24px;
        }
        .sidebar span {
            transition: opacity 0.3s;
        }
        .sidebar.collapsed span {
            opacity: 0;
        }
        .content {
            flex: 1;
            padding: 40px;
            background: #fff;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background: linear-gradient(135deg, #e6e6e6, #f1f1f1);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: padding 0.3s;
        }
        .header.collapsed {
            padding: 10px;
        }
        .header-title {
            height: 20px;
            margin-left: 30px;
            display: flex;
            align-items: center;
            flex-grow: 1;
            font-size: 20px;
            font-weight: 600;
            text-align: center;
            transition: opacity 0.3s, visibility 0.3s;
        }
        .header-title span {
            margin-right: 105px; /* Ajusta el margen según sea necesario */
        }
        .header-title.collapsed {
            opacity: 0;
            visibility: hidden;
        }
        .header-icon {
            font-size: 24px;
            text-align: center;
            transition: opacity 0.3s, visibility 0.3s;
            opacity: 0;
            visibility: hidden;
        }
        .header-icon.collapsed {
            opacity: 1;
            visibility: visible;
        }
        .toggle-btn {
            background: #333;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .toggle-btn:hover {
            background: #555;
        }

        @media (min-width: 800px) {
            .sidebar {
                width: 30%;
                height: auto;
                box-shadow: none;
                padding: 20px 30px;
                margin-top: 50px;
            }
            .sidebar.collapsed {
                width: 30px;
                height: auto;
                align-items: center;
                padding: 10px;
                margin-top: 50px;
            }
            .sidebar.collapsed h2 {
                margin-top: 10px;
                display: none;
            }
            .sidebar.collapsed a {
                justify-content: center;
                padding: 10px 0;
            }
            .sidebar.collapsed span {
                display: none;
            }
            .content {
                padding: 30px;
                padding-top: 80px; /* To avoid content being hidden behind the fixed header */
            }
            .header {
                padding: 10px 10px;
            }
            .toggle-btn {
                padding: 8px;
            }
        }
        @media (max-width: 800px) {
            .sidebar {
                width: 30%;
                height: auto;
                box-shadow: none;
                padding: 20px 30px;
                margin-top: 50px;
            }
            .sidebar.collapsed {
                width: 30px;
                height: auto;
                align-items: center;
                padding: 10px;
                margin-top: 50px;
            }
            .sidebar.collapsed h2 {
                margin-top: 10px;
                display: none;
            }
            .sidebar.collapsed a {
                justify-content: center;
                padding: 10px 0;
            }
            .sidebar.collapsed span {
                display: none;
            }
            .content {
                padding: 30px;
                padding-top: 80px; /* To avoid content being hidden behind the fixed header */
            }
            .header {
                padding: 10px 10px;
            }
            .toggle-btn {
                padding: 8px;
            }
        }
        footer {
            text-align: center;
            padding: 10px;
            font-size: 14px;
            background: #333;
            color: #fff;
            width: 100%;
            position: absolute;
            bottom: 0;
        }
        #content {
            flex: 1;
            align-items: center;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
        }

    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/instascan/1.0.0/instascan.min.js"></script>
</head>
<body>
    <div class="header">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <div class="header-title">
            <span>Safe Access</span>
            <h6 class="titulo"><?php echo htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8'); ?></h6>
        </div>
        <div class="header-icon"><i class="fas fa-lock"></i></div>
    </div>
    <div class="main-container">
        <div class="sidebar" id="sidebar">
            <a href="#" onclick="loadContent('empleados.php')"><i class="fas fa-user"></i><span>Gestionar Usuarios</span></a>
            <a href="#" onclick="loadContent('ambientes.php')"><i class="fas fa-calendar-alt"></i><span>Areas</span></a>
            <a href="#" onclick="loadContent('Lector.php')"><i class="fas fa-calendar-alt"></i><span>Areas</span></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Cerrar sesión</span></a>
        </div>
        <div id="content"></div>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const header = document.querySelector('.header');
            const headerTitle = document.querySelector('.header-title');
            const headerIcon = document.querySelector('.header-icon');
            sidebar.classList.toggle('collapsed');
            header.classList.toggle('collapsed');
            headerTitle.classList.toggle('');
            headerIcon.classList.toggle('');
        }

        function changeContent(option) {
            const contentDiv = document.getElementById('content');
            let content = '';
            switch(option) {
                case 'perfil':
                    content = `<h1>Perfil</h1><p>Contenido del perfil aquí.</p>`;
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

    <footer>
        &copy; 2024 Safe Access. Todos los derechos reservados.
    </footer>
</body>
</html>

