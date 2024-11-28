<?php
require_once('../../config/db.php');
session_start();

// Verificar si el usuario está autenticado y es administrador
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type: application/json");

    if (isset($_POST["create_user"])) {
        // Código para crear un nuevo empleado
        $documento = intval($_POST["documento"]);
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"];
        $correo = $_POST["correo"];
        $estado = isset($_POST["estado"]) ? 1 : 0;
        $cargo = intval($_POST["cargo"]);
        $contrasena = $_POST["contrasena"];

        if (empty($contrasena)) {
            echo json_encode(["status" => "error", "message" => "La contraseña es obligatoria"]);
            exit;
        }

        // Hashear la contraseña
        $hashed_password = password_hash($contrasena, PASSWORD_BCRYPT);

        try {
            // Verificar si el documento ya existe
            $checkQuery = "SELECT COUNT(*) AS count FROM tbl_empleado WHERE emp_documento = ?";
            $stmt = $db->prepare($checkQuery);
            $stmt->bind_param("i", $documento);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row['count'] > 0) {
                echo json_encode(["status" => "error", "message" => "El documento ya existe"]);
                exit;
            }

            // Insertar el nuevo empleado
            $query = "INSERT INTO tbl_empleado (emp_documento, emp_nombre, emp_apellidos, emp_contrasena, empleado_correo, emp_estado, emp_cargo) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("issssis", $documento, $nombre, $apellidos, $hashed_password, $correo, $estado, $cargo);
            $stmt->execute();

            echo json_encode(["status" => "success", "message" => "Usuario creado con éxito"]);
            exit;
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            exit;
        }
    }

    elseif (isset($_POST["update_employee"])) {
        // Código para actualizar empleado
        $documento = intval($_POST["documento"]);
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"];
        $correo = $_POST["correo"];
        $estado = isset($_POST["estado"]) ? 1 : 0;
        $cargo = intval($_POST["cargo"]);
        $contrasena = $_POST["contrasena"];

        try {
            if (!empty($contrasena)) {
                // Si se proporciona una nueva contraseña, actualizarla
                $hashed_password = password_hash($contrasena, PASSWORD_BCRYPT);
                $query = "UPDATE tbl_empleado SET emp_nombre = ?, emp_apellidos = ?, empleado_correo = ?, emp_estado = ?, emp_cargo = ?, emp_contrasena = ? WHERE emp_documento = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("sssiisi", $nombre, $apellidos, $correo, $estado, $cargo, $hashed_password, $documento);
            } else {
                // Si no, actualizar sin cambiar la contraseña
                $query = "UPDATE tbl_empleado SET emp_nombre = ?, emp_apellidos = ?, empleado_correo = ?, emp_estado = ?, emp_cargo = ? WHERE emp_documento = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("sssiis", $nombre, $apellidos, $correo, $estado, $cargo, $documento);
            }

            $stmt->execute();

            echo json_encode(["status" => "success", "message" => "Empleado actualizado con éxito"]);
            exit;
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            exit;
        }
    }
    elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        header("Content-Type: application/json");
    
        if (isset($_POST["toggle_employee"])) {
            // Código para activar/inactivar empleado
            $documento = intval($_POST["documento"]);
            $estado = intval($_POST["estado"]);
    
            try {
                $query = "UPDATE tbl_empleado SET emp_estado = ? WHERE emp_documento = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("ii", $estado, $documento);
                $stmt->execute();
    
                echo json_encode(["status" => "success", "message" => "Estado del empleado actualizado"]);
                exit;
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                exit;
            }
        }
    }
}

try {
    // Obtener la lista de empleados
    $query = "SELECT emp_documento, emp_nombre, emp_apellidos, empleado_correo, emp_estado, emp_cargo FROM tbl_empleado";
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
    <title>Lista de Empleados</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body, html {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background-color: #f5f7fa;
            overflow-x: hidden;
        }
        .container {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            font-size: 28px;
            font-weight: 600;
            color: #2d3436;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-create-user {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            background-color: #0984e3;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }
        .btn-create-user:hover {
            background-color: #74b9ff;
        }

        /* Table Styles for Desktop */
        .employee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: table;
        }
        .employee-table th, .employee-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 15px;
            color: #2d3436;
        }
        .employee-table th {
            background-color: #dfe6e9;
            font-weight: 600;
        }
        .employee-table tbody tr:hover {
            background-color: #f1f3f5;
        }
        .employee-table td:last-child {
            text-align: center;
        }
        .employee-table button {
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 5px;
            transition: background 0.3s;
        }
        .employee-table .btn-toggle {
            background-color: #636e72;
        }
        .employee-table .btn-toggle:hover {
            background-color: #b2bec3;
        }
        .employee-table .btn-modify {
            background-color: #00b894;
        }
        .employee-table .btn-modify:hover {
            background-color: #55efc4;
        }

        /* Form Styles */
        .form-container {
            display: none;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
            animation: fadeIn 0.5s ease-in-out;
        }
        .form-container h2 {
            font-size: 22px;
            font-weight: 600;
            color: #2d3436;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
        }
        .form-container label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2d3436;
        }
        .form-container input, .form-container select {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 15px;
        }
        .form-container button {
            padding: 10px;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            background-color: #0984e3;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .form-container button:hover {
            background-color: #74b9ff;
        }

        /* Responsive Styles for Mobile */
        @media (max-width: 768px) {
            h1 {
                font-size: 24px;
            }
            .btn-create-user {
                font-size: 14px;
                padding: 8px 15px;
            }
            .employee-table, .employee-table thead, .employee-table tbody, .employee-table th, .employee-table td, .employee-table tr {
                display: block;
                width: 100%;
            }
            .employee-table thead {
                display: none;
            }
            .employee-table tbody tr {
                margin-bottom: 15px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                padding: 15px;
                background: #fff;
            }
            .employee-table td {
                display: flex;
                justify-content: space-between;
                padding: 10px;
                border-bottom: none;
                font-size: 14px;
            }
            .employee-table td:before {
                content: attr(data-label);
                font-weight: 600;
                color: #2d3436;
                flex-basis: 40%;
                text-align: left;
            }
            .employee-table button {
                font-size: 12px;
                padding: 6px 10px;
            }
            .form-container {
                width: 90%;
                padding: 15px;
            }
            .form-container input, .form-container select {
                font-size: 14px;
                padding: 8px;
            }
        }

        /* Fade-in animation */
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
    </style>
    <script>
        function toggleForm() {
        const formContainer = document.getElementById('form-container');
        const employeeTable = document.getElementById('employee-table');
        formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
        employeeTable.style.display = formContainer.style.display === 'none' ? 'table' : 'none';
    }

    function createUser(event) {
        event.preventDefault();
        const form = document.getElementById('create-user-form');
        const formData = new FormData(form);

        fetch('empleados.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Usuario creado con éxito');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function toggleEmployee(documento, estado) {
        const newState = estado === '1' ? '0' : '1';
        const formData = new FormData();
        formData.append('toggle_employee', '1');
        formData.append('documento', documento);
        formData.append('estado', newState);

        fetch('empleados.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Estado del empleado actualizado');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function loadEmployeeData(documento, nombre, apellidos, correo, estado, cargo) {
        const formContainer = document.getElementById('update-form-container');
        const employeeTable = document.getElementById('employee-table');
        formContainer.style.display = 'block';
        employeeTable.style.display = 'none';

        const form = document.getElementById('update-user-form');
        form.elements['documento'].value = documento;
        form.elements['nombre'].value = nombre;
        form.elements['apellidos'].value = apellidos;
        form.elements['correo'].value = correo;
        form.elements['update-estado'].checked = estado === '1';
        form.elements['cargo'].value = cargo;
    }

    function updateEmployee(event) {
        event.preventDefault();
        const confirmation = confirm("¿Estás seguro de que deseas modificar este usuario?");
        if (confirmation) {
            const form = document.getElementById('update-user-form');
            const formData = new FormData(form);

            fetch('empleados.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Usuario actualizado con éxito');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    }

    </script>
</head>
<body>
    <div class="container">
        <h1>Lista de Empleados</h1>
        <button class="btn-create-user" onclick="toggleForm()">Crear Usuario</button>

        <div id="form-container" class="form-container" style="display: none;">
            <h2>Crear Nuevo Usuario</h2>
            <form id="create-user-form" onsubmit="createUser(event)">
                <input type="hidden" name="create_user" value="1">
                <label for="documento">Documento:</label>
                <input type="text" id="documento" name="documento" required>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required>
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required>
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
                <label for="estado">Estado:</label>
                <input type="checkbox" id="estado" name="estado">
                <label for="cargo">Cargo:</label>
                <select id="cargo" name="cargo" required>
                    <option value="1">Empleado</option>
                    <option value="2">Administrador</option>
                </select>
                <button type="submit">Crear Usuario</button>
            </form>
        </div>

        <div id="update-form-container" class="form-container" style="display: none;">
            <h2>Modificar Usuario</h2>
            <form id="update-user-form" onsubmit="updateEmployee(event)">
                <input type="hidden" name="update_employee" value="1">
                <label for="documento">Documento:</label>
                <input type="text" id="update-documento" name="documento" readonly>
                <label for="nombre">Nombre:</label>
                <input type="text" id="update-nombre" name="nombre" required>
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="update-apellidos" name="apellidos" required>
                <label for="correo">Correo:</label>
                <input type="email" id="update-correo" name="correo" required>
                <label for="contrasena">Nueva Contraseña (opcional):</label>
                <input type="password" id="update-contrasena" name="contrasena">
                <label for="estado">Estado:</label>
                <input type="checkbox" id="update-estado" name="estado">
                <label for="cargo">Cargo:</label>
                <select id="update-cargo" name="cargo" required>
                    <option value="1">Empleado</option>
                    <option value="2">Administrador</option>
                </select>
                <button type="submit">Modificar Usuario</button>
            </form>
        </div>

        <table id="employee-table" class="employee-table">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Correo</th>
                    <th>Estado</th>
                    <th>Cargo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Documento"><?php echo htmlspecialchars($row['emp_documento']); ?></td>
                        <td data-label="Nombre"><?php echo htmlspecialchars($row['emp_nombre']); ?></td>
                        <td data-label="Apellidos"><?php echo htmlspecialchars($row['emp_apellidos']); ?></td>
                        <td data-label="Correo"><?php echo htmlspecialchars($row['empleado_correo']); ?></td>
                        <td data-label="Estado"><?php echo $row['emp_estado'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
                        <td data-label="Cargo"><?php echo $row['emp_cargo'] == 1 ? 'Empleado' : 'Administrador'; ?></td>
                        <td>
                            <button class="btn-toggle" onclick="toggleEmployee('<?php echo $row['emp_documento']; ?>', '<?php echo $row['emp_estado']; ?>')">
                                <?php echo $row['emp_estado'] == 1 ? 'Inactivar' : 'Activar'; ?>
                            </button>
                            <button class="btn-modify" onclick="loadEmployeeData('<?php echo $row['emp_documento']; ?>', '<?php echo $row['emp_nombre']; ?>', '<?php echo $row['emp_apellidos']; ?>', '<?php echo $row['empleado_correo']; ?>', '<?php echo $row['emp_estado']; ?>', '<?php echo $row['emp_cargo']; ?>')">
                                Modificar
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
