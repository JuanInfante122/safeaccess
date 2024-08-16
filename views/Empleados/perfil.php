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
            width: 50%;
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

        .profile-container p {
            font-size: 18px;
            color: #555;
            line-height: 1.6;
        }

        .profile-container p strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Información del Empleado</h2>
        <p><strong>Documento:</strong> <?php echo htmlspecialchars($row['emp_documento'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($row['emp_nombre'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Apellidos:</strong> <?php echo htmlspecialchars($row['emp_apellidos'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Cargo:</strong> <?php echo htmlspecialchars($row['emp_cargo'], ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
</body>
</html>
