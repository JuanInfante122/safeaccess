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
$sql = "SELECT 
            tbl_creacion_acceso.acceso_fecha, 
            tbl_creacion_acceso.acceso_estado, 
            tbl_area.area_nombre 
        FROM 
            tbl_creacion_acceso 
        INNER JOIN 
            tbl_area 
        ON 
            tbl_creacion_acceso.acceso_area = tbl_area.area_id 
        WHERE 
            tbl_creacion_acceso.acceso_empleado = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$stmt->bind_param("s", $documentoEmpleado);
if (!$stmt->execute()) {
    die("Error en la ejecución de la consulta: " . $stmt->error);
}

$result = $stmt->get_result();
$accesos = [];
if ($result->num_rows > 0) {
    // Si se encuentran accesos, obtén la información
    while ($row = $result->fetch_assoc()) {
        $accesos[] = $row;
    }
} else {
    // Si no se encuentran accesos, redirige a una página de error o muestra un mensaje
    echo "No se encontraron accesos para el empleado.";
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
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .profile-container {
            width: 60%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .profile-container h2 {
            text-align: center;
            color: #444;
            margin-bottom: 20px;
        }

        .access-record {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .access-record:last-child {
            border-bottom: none;
        }

        .access-record p {
            font-size: 16px;
            color: #555;
            margin: 5px 0;
        }

        .access-record p strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Accesos del Empleado</h2>
        <?php foreach ($accesos as $acceso): ?>
        <div class="access-record">
            <p><strong>Fecha de creación:</strong> <?php echo htmlspecialchars($acceso['acceso_fecha'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Estado:</strong> <?php echo $acceso['acceso_estado'] == 1 ? 'activo' : 'inactivo'; ?></p>
            <p><strong>Nombre Area:</strong> <?php echo htmlspecialchars($acceso['area_nombre'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
