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
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
    /* General Styles */
    body, html {
        height: 100%;
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background-color: #f5f5f5;
        color: #333;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
    }

    .main-container {
        display: flex;
        height: 100%;
        width: 100%;
    }

    /* Sidebar Styles */
    .sidebar {
        width: 250px;
        background-color: #333;
        color: #fff;
        display: flex;
        flex-direction: column;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        transition: width 0.3s ease;
    }
    .sidebar.collapsed {
        width: 80px;
        padding: 20px 5px;
        align-items: center;
    }
    .sidebar h2 {
        margin-bottom: 30px;
        font-size: 20px;
        text-align: center;
        color: #fff;
        transition: opacity 0.3s;
    }
    .sidebar.collapsed h2 {
        opacity: 0;
    }
    .sidebar a {
        color: #ddd;
        text-decoration: none;
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 5px;
        display: flex;
        align-items: center;
        transition: background 0.3s;
    }
    .sidebar a:hover {
        background: #444;
    }
    .sidebar i {
        margin-right: 10px;
        font-size: 18px;
    }
    .sidebar.collapsed i {
        margin-right: 0;
    }
    .sidebar.collapsed span {
        display: none;
    }

    /* Header Styles */
    .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 20px;
        background-color: #444;
        color: #fff;
        width: calc(100% - 250px);
        position: fixed;
        top: 0;
        right: 0;
        height: 60px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        transition: width 0.3s ease;
    }
    .header.collapsed {
        width: calc(100% - 80px);
    }
    .header-title {
        font-size: 18px;
        font-weight: 500;
        flex-grow: 1;
        text-align: center;
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
        background: #666;
    }

    /* Content Styles */
    .content {
        flex: 1;
        padding: 30px;
        margin-top: 60px;
        background: #fff;
        overflow-y: auto;
    }
    #content {
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    /* Footer Styles */
    footer {
        text-align: center;
        padding: 10px;
        font-size: 14px;
        background: #333;
        color: #fff;
        width: 100%;
        position: relative;
        bottom: 0;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.15);
    }

    /* Responsive Design */
    @media (max-width: 800px) {
        .sidebar {
            width: 80px;
            padding: 20px 5px;
        }
        .header {
            width: calc(100% - 80px);
        }
        .content {
            padding: 20px;
        }
    }
</style>

</head>
<body>
    <div class="header">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <div class="header-title">Safe Access - Admin</div>
    </div>
    <div class="main-container">
        <div class="sidebar" id="sidebar">
            <h2>Safe Access</h2>
            <a href="#" onclick="loadContent('empleados.php')"><i class="fas fa-users"></i><span>Gestionar Usuarios</span></a>
            <a href="#" onclick="loadContent('ambientes.php')"><i class="fas fa-building"></i><span>Áreas</span></a>
            <a href="#" onclick="loadContent('Lector.php')"><i class="fas fa-qrcode"></i><span>Lector QR</span></a>
            <a href="#" onclick="loadContent('accesos.php')"><i class="fas fa-qrcode"></i><span>Accesos</span></a>
            <a href="#" onclick="loadContent('areas.php')"><i class="fas fa-qrcode"></i><span>Áreas en tiempo real</span></a>
            <a href="#" onclick="loadContent('visualizacion_accesos.php')"><i class="fas fa-qrcode"></i><span>Visualización de Accesos</span></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Cerrar sesión</span></a>
        </div>
        <div class="content" id="content">
            <div id="content">
                <h1>Bienvenido, Administrador</h1>
                <p>Seleccione una opción del menú para gestionar los recursos.</p>
            </div>
        </div>
    </div>

    <footer class="footer">
        &copy; 2024 Safe Access. Todos los derechos reservados.
    </footer>

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
</body>
</html>