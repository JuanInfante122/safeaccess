<?php
session_start();
require_once('../../config/db.php');

// Verifica si la sesión está iniciada y contiene el username
if (!isset($_SESSION['username'])) {
    die("Acceso no autorizado.");
}

// Obtén el documento del empleado de la sesión
$documentoEmpleado = $_SESSION['username'];

// Conecta a la base de datos
$conexion = new mysqli("localhost", "root", "", "safeacces"); // Cambia estos datos según tu configuración
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Consulta la información del empleado en la base de datos
$sql = "SELECT * FROM tbl_empleado WHERE emp_documento = ?";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$stmt->bind_param("s", $documentoEmpleado);
if (!$stmt->execute()) {
    die("Error en la ejecución de la consulta: " . $stmt->error);
}

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    // Si se encuentra el empleado, obtén su información
    $row = $result->fetch_assoc();
} else {
    // Si no se encuentra el empleado, redirige a una página de error o muestra un mensaje
    echo "Empleado no encontrado.";
    $stmt->close();
    $conexion->close();
    exit();
}

// Cierra la conexión a la base de datos
$stmt->close();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - Carnet</title>
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

        /* Badge Container */
        .badge-container {
            background: #fff;
            width: 320px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            border: 2px solid #333;
            position: relative;
        }

        /* Header */
        .badge-header {
            background: #333;
            color: #fff;
            padding: 10px;
            border-radius: 12px 12px 0 0;
            font-size: 18px;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* Profile Image Placeholder */
        .profile-image {
            width: 100px;
            height: 100px;
            background: #ddd;
            border-radius: 50%;
            margin: 15px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #666;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Profile Details */
        .profile-detail {
            font-size: 15px;
            color: #555;
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            background: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .profile-detail strong {
            color: #333;
            font-weight: 500;
        }

        /* Footer */
        .badge-footer {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Responsive Design */
        @media (max-width: 400px) {
            .badge-container {
                width: 90%;
                padding: 15px;
            }
            .badge-header {
                font-size: 16px;
            }
            .profile-image {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="badge-container">
        <div class="badge-header">Empleado - Safe Access</div>
        <div class="profile-image">Foto</div>
        <div class="profile-detail"><strong>Documento:</strong> <span><?php echo htmlspecialchars($row['emp_documento'], ENT_QUOTES, 'UTF-8'); ?></span></div>
        <div class="profile-detail"><strong>Nombre:</strong> <span><?php echo htmlspecialchars($row['emp_nombre'], ENT_QUOTES, 'UTF-8'); ?></span></div>
        <div class="profile-detail"><strong>Apellidos:</strong> <span><?php echo htmlspecialchars($row['emp_apellidos'], ENT_QUOTES, 'UTF-8'); ?></span></div>
        <div class="profile-detail"><strong>Cargo:</strong> 
            <span>
                <?php
                    // Display 'Administrador' for cargo = 2, 'Empleado' for cargo = 1
                    echo $row['emp_cargo'] == 2 ? 'Administrador' : 'Empleado';
                ?>
            </span>
        </div>
        <div class="badge-footer">&copy; 2024 Safe Access. Todos los derechos reservados.</div>
    </div>
</body>
</html>
