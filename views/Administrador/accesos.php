<?php
require_once('../../config/db.php');
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
    header("Content-Type: application/json");
    echo json_encode(["status" => "error", "message" => "Sesión no iniciada"]);
    exit;
}

$username = $_SESSION["username"];

// Conexión a la base de datos
try {
    $db = Database::connect();
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error en la conexión a la base de datos"]);
    exit;
}

// Manejo de las solicitudes POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type: application/json");

    if (isset($_POST["action"])) {
        $action = $_POST["action"];

        if ($action === "create_access") {
            // Crear un nuevo acceso
            $empleado = intval($_POST["empleado"]);
            $area = intval($_POST["area"]);
            $fecha = $_POST["fecha"];
            $hora = $_POST["hora"];
            $estado = 1; // Activo por defecto

            try {
                $query = "INSERT INTO tbl_creacion_acceso (acceso_empleado, acceso_area, acceso_fecha, acceso_hora, acceso_estado) 
                          VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->bind_param("iissi", $empleado, $area, $fecha, $hora, $estado);
                $stmt->execute();

                echo json_encode(["status" => "success", "message" => "Acceso creado con éxito"]);
                exit;
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                exit;
            }
        } elseif ($action === "update_access") {
            // Actualizar un acceso
            $id = intval($_POST["id"]);
            $empleado = intval($_POST["empleado"]);
            $area = intval($_POST["area"]);
            $fecha = $_POST["fecha"];
            $hora = $_POST["hora"];

            try {
                $query = "UPDATE tbl_creacion_acceso SET acceso_empleado = ?, acceso_area = ?, acceso_fecha = ?, acceso_hora = ? 
                          WHERE acceso_id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("iissi", $empleado, $area, $fecha, $hora, $id);
                $stmt->execute();

                echo json_encode(["status" => "success", "message" => "Acceso actualizado con éxito"]);
                exit;
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                exit;
            }
        } elseif ($action === "toggle_access") {
            // Activar/Inactivar un acceso
            $id = intval($_POST["id"]);
            $estado = intval($_POST["estado"]);

            try {
                $query = "UPDATE tbl_creacion_acceso SET acceso_estado = ? WHERE acceso_id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("ii", $estado, $id);
                $stmt->execute();

                echo json_encode(["status" => "success", "message" => "Estado del acceso actualizado"]);
                exit;
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                exit;
            }
        }
    }
}

// Obtener la lista de accesos
try {
    $query = "
        SELECT c.acceso_id, c.acceso_hora, c.acceso_fecha, c.acceso_estado, 
               e.emp_documento AS empleado_id, CONCAT(e.emp_nombre, ' ', e.emp_apellidos) AS empleado_nombre,
               a.area_id, a.area_nombre 
        FROM tbl_creacion_acceso c
        INNER JOIN tbl_empleado e ON c.acceso_empleado = e.emp_documento
        INNER JOIN tbl_area a ON c.acceso_area = a.area_id";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    $error_message = "Error al conectar a la base de datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Accesos</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        button {
            padding: 10px 15px;
            margin: 5px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function toggleForm(action = "create_access", data = {}) {
            const form = document.getElementById('accessForm');
            form.style.display = 'block';
            document.getElementById('actionType').value = action;

            // Reset form for create
            if (action === "create_access") {
                form.reset();
                document.getElementById('accessId').value = '';
            }

            // Populate form for edit
            if (action === "update_access") {
                document.getElementById('accessId').value = data.id;
                document.getElementById('empleado').value = data.empleado;
                document.getElementById('area').value = data.area;
                document.getElementById('fecha').value = data.fecha;
                document.getElementById('hora').value = data.hora;
            }
        }

        function submitForm(event) {
            event.preventDefault();
            const form = document.getElementById('accessForm');
            const formData = new FormData(form);

            fetch('accesos.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            }).catch(error => console.error('Error:', error));
        }

        function toggleAccess(id, estado) {
            const formData = new FormData();
            formData.append('action', 'toggle_access');
            formData.append('id', id);
            formData.append('estado', estado);

            fetch('accesos.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            }).catch(error => console.error('Error:', error));
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Gestión de Accesos</h1>
        <button onclick="toggleForm()">Crear Acceso</button>

        <form id="accessForm" style="display:none;" onsubmit="submitForm(event)">
            <input type="hidden" id="actionType" name="action" value="create_access">
            <input type="hidden" id="accessId" name="id">
            <label for="empleado">Empleado:</label>
            <select id="empleado" name="empleado" required>
                <option value="">Seleccione un empleado</option>
                <?php
                $empleados = $db->query("SELECT emp_documento, CONCAT(emp_nombre, ' ', emp_apellidos) AS nombre FROM tbl_empleado WHERE emp_estado = 1");
                while ($row = $empleados->fetch_assoc()) {
                    echo "<option value='{$row['emp_documento']}'>{$row['nombre']}</option>";
                }
                ?>
            </select>
            <label for="area">Área:</label>
            <select id="area" name="area" required>
                <option value="">Seleccione un área</option>
                <?php
                $areas = $db->query("SELECT area_id, area_nombre FROM tbl_area WHERE area_estado = 1");
                while ($row = $areas->fetch_assoc()) {
                    echo "<option value='{$row['area_id']}'>{$row['area_nombre']}</option>";
                }
                ?>
            </select>
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" required>
            <label for="hora">Hora:</label>
            <input type="time" id="hora" name="hora" required>
            <button type="submit">Guardar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Área</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['empleado_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['area_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['acceso_fecha']); ?></td>
                        <td><?php echo htmlspecialchars($row['acceso_hora']); ?></td>
                        <td><?php echo $row['acceso_estado'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
                        <td>
                            <button onclick="toggleForm('update_access', {
                                id: '<?php echo $row['acceso_id']; ?>',
                                empleado: '<?php echo $row['empleado_id']; ?>',
                                area: '<?php echo $row['area_id']; ?>',
                                fecha: '<?php echo $row['acceso_fecha']; ?>',
                                hora: '<?php echo $row['acceso_hora']; ?>'
                            })">Editar</button>
                            <button onclick="toggleAccess('<?php echo $row['acceso_id']; ?>', '<?php echo $row['acceso_estado'] == 1 ? 0 : 1; ?>')">
                                <?php echo $row['acceso_estado'] == 1 ? 'Inactivar' : 'Activar'; ?>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
