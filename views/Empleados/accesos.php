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
    <title>Accesos del Empleado</title>
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
            align-items: flex-start;
            min-height: 100vh;
            color: #333;
            padding: 20px 0;
        }

        /* Main Container */
        .access-container {
            background: #fff;
            width: 100%;
            max-width: 600px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            border: 2px solid #333;
        }

        /* Header */
        .access-header {
            background: #333;
            color: #fff;
            padding: 10px;
            border-radius: 12px 12px 0 0;
            font-size: 18px;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* Access Record */
        .access-record {
            background: #f9f9f9;
            padding: 15px;
            margin: 15px 0;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .access-record p {
            font-size: 16px;
            color: #555;
            margin: 8px 0;
        }

        .access-record p strong {
            color: #333;
            font-weight: 500;
        }

        .access-status {
            font-weight: 600;
            color: #28a745;
        }
        .access-status.inactive {
            color: #dc3545;
        }

        /* Responsive Design */
        @media (max-width: 400px) {
            .access-container {
                width: 90%;
                padding: 15px;
            }
            .access-header {
                font-size: 16px;
            }
            .access-record p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="access-container">
        <div class="access-header">Accesos del Empleado</div>
        <?php if (count($accesos) > 0): ?>
            <?php foreach ($accesos as $acceso): ?>
                <div class="access-record">
                    <p><strong>Fecha de creación:</strong> <?php echo htmlspecialchars($acceso['acceso_fecha'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Estado:</strong> 
                        <span class="access-status <?php echo $acceso['acceso_estado'] == 1 ? '' : 'inactive'; ?>">
                            <?php echo $acceso['acceso_estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </p>
                    <p><strong>Área:</strong> <?php echo htmlspecialchars($acceso['area_nombre'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron accesos para el empleado.</p>
        <?php endif; ?>
    </div>
</body>
</html>
