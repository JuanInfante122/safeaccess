<?php
// Manejo de operaciones CRUD
$conn = new mysqli("localhost", "root", "", "safeacces");
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'create') {
        $employee = $_POST['employee'];
        $area = $_POST['area'];
        $date = $_POST['date'];
        $time = $_POST['time'];

        $createQuery = "INSERT INTO tbl_creacion_acceso (acceso_empleado, acceso_area, acceso_fecha, acceso_hora, acceso_estado) VALUES ('$employee', '$area', '$date', '$time', 1)";
        $conn->query($createQuery);
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $employee = $_POST['employee'];
        $area = $_POST['area'];
        $date = $_POST['date'];
        $time = $_POST['time'];

        $editQuery = "UPDATE tbl_creacion_acceso SET acceso_empleado = '$employee', acceso_area = '$area', acceso_fecha = '$date', acceso_hora = '$time' WHERE acceso_id = $id";
        $conn->query($editQuery);
    } elseif ($action === 'toggle') {
        $id = $_POST['id'];

        // Cambiar el estado actual del acceso
        $toggleQuery = "UPDATE tbl_creacion_acceso SET acceso_estado = NOT acceso_estado WHERE acceso_id = $id";
        $conn->query($toggleQuery);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Accesos</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        /* Aquí reutilizamos los estilos del ejemplo anterior */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        form div {
            margin-bottom: 15px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        form input, form select, form button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form button {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Gestión de Accesos</h1>

    <!-- Formulario para Crear/Modificar Accesos -->
    <form id="accessForm">
        <input type="hidden" id="accessId" name="id" value="">
        <input type="hidden" id="actionType" name="action" value="create">
        <div>
            <label for="employee">Empleado</label>
            <select id="employee" name="employee" required>
                <option value="">Seleccione un empleado</option>
                <?php
                $employeesQuery = "SELECT emp_documento, CONCAT(emp_nombre, ' ', emp_apellidos) AS nombre_completo FROM tbl_empleado WHERE emp_estado = 1";
                $employeesResult = $conn->query($employeesQuery);
                while ($row = $employeesResult->fetch_assoc()) {
                    echo "<option value='{$row['emp_documento']}'>{$row['nombre_completo']}</option>";
                }
                ?>
            </select>
        </div>

        <div>
            <label for="area">Área</label>
            <select id="area" name="area" required>
                <option value="">Seleccione un área</option>
                <?php
                $areasQuery = "SELECT area_id, area_nombre FROM tbl_area WHERE area_estado = 1";
                $areasResult = $conn->query($areasQuery);
                while ($row = $areasResult->fetch_assoc()) {
                    echo "<option value='{$row['area_id']}'>{$row['area_nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <div>
            <label for="date">Fecha</label>
            <input type="date" id="date" name="date" required>
        </div>

        <div>
            <label for="time">Hora</label>
            <input type="time" id="time" name="time" required>
        </div>

        <button type="button" id="saveButton">Guardar Acceso</button>
    </form>

    <!-- Tabla de Accesos -->
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
        <tbody id="accessTable">
            <?php
            $accessQuery = "
                SELECT 
                    c.acceso_id,
                    CONCAT(e.emp_nombre, ' ', e.emp_apellidos) AS empleado,
                    a.area_nombre AS area,
                    c.acceso_fecha AS fecha,
                    c.acceso_hora AS hora,
                    c.acceso_estado AS estado
                FROM tbl_creacion_acceso c
                INNER JOIN tbl_empleado e ON c.acceso_empleado = e.emp_documento
                INNER JOIN tbl_area a ON c.acceso_area = a.area_id
            ";
            $accessResult = $conn->query($accessQuery);

            if ($accessResult->num_rows > 0) {
                while ($row = $accessResult->fetch_assoc()) {
                    echo "
                        <tr>
                            <td>{$row['empleado']}</td>
                            <td>{$row['area']}</td>
                            <td>{$row['fecha']}</td>
                            <td>{$row['hora']}</td>
                            <td>" . ($row['estado'] ? "Activo" : "Inactivo") . "</td>
                            <td>
                                <button onclick=\"editAccess({$row['acceso_id']}, '{$row['empleado']}', '{$row['area']}', '{$row['fecha']}', '{$row['hora']}')\">Editar</button>
                                <button onclick=\"toggleAccess({$row['acceso_id']})\">" . ($row['estado'] ? "Inactivar" : "Activar") . "</button>
                            </td>
                        </tr>
                    ";
                }
            } else {
                echo "<tr><td colspan='6'>No hay accesos registrados</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>

    <script>
        document.getElementById('saveButton').addEventListener('click', () => {
            const formData = new FormData(document.getElementById('accessForm'));
            axios.post('', formData).then(() => location.reload());
        });

        function editAccess(id, employee, area, date, time) {
            document.getElementById('accessId').value = id;
            document.getElementById('employee').value = employee;
            document.getElementById('area').value = area;
            document.getElementById('date').value = date;
            document.getElementById('time').value = time;
            document.getElementById('actionType').value = 'edit';
        }

        function toggleAccess(id) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('action', 'toggle');
            axios.post('', formData).then(() => location.reload());
        }
    </script>
</body>
</html>
