<?php
require_once('../../config/db.php');
session_start();

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION["username"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create_user"])) {
        // Código para crear un nuevo empleado
        // ...

    } elseif (isset($_POST["toggle_employee"])) {
        // Código para activar/inactivar al empleado
        $documento = $_POST["documento"];
        $estado = $_POST["estado"];

        try {
            // Conectar a la base de datos
            $db = Database::connect();

            // Preparar la consulta SQL para actualizar el estado del empleado
            $query = "UPDATE tbl_empleado SET emp_estado = ? WHERE emp_documento = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("is", $estado, $documento);
            $stmt->execute();

            // Cerrar la conexión a la base de datos
            $stmt->close();
            $db->close();

            echo json_encode(["status" => "success"]);
            exit;
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            exit;
        }
    } elseif (isset($_POST["update_employee"])) {
        // Código para modificar el empleado
        $documento = $_POST["documento"];
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"];
        $contrasena = $_POST["contrasena"];
        $estado = isset($_POST["estado"]) ? 1 : 0; // Verificar si el estado está marcado o no
        $cargo = $_POST["cargo"];

        try {
            // Conectar a la base de datos
            $db = Database::connect();

            // Preparar la consulta SQL para actualizar los datos del empleado
            $query = "UPDATE tbl_empleado SET emp_nombre = ?, emp_apellidos = ?, emp_contrasena = ?, emp_estado = ?, emp_cargo = ? WHERE emp_documento = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("sssiis", $nombre, $apellidos, $contrasena, $estado, $cargo, $documento);
            $stmt->execute();

            // Cerrar la conexión a la base de datos
            $stmt->close();
            $db->close();

            echo json_encode(["status" => "success"]);
            exit;
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            exit;
        }
    }
}

try {
    // Conectar a la base de datos
    $db = Database::connect();

    // Preparar la consulta SQL para obtener los empleados
    $query = "SELECT emp_documento, emp_nombre, emp_apellidos, emp_contrasena, emp_estado, emp_cargo FROM tbl_empleado";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cerrar la conexión a la base de datos
    $stmt->close();
    $db->close();
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
    body, html {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        color: #000; /* letras negras */
        background-color: #fff; /* fondo blanco */
    }
    .employee-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .employee-table th, .employee-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    .employee-table th {
        background-color: #eee; /* gris claro */
    }
    .btn-create-user {
        display: inline-block;
        margin-bottom: 20px;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: 600;
        color: white; /* letras negras */
        background-color: #666; /* negro */
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-create-user:hover {
        background-color: #666; /* gris oscuro */
    }
    .form-container {
        display: none;
        margin-top: 20px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #fff; /* fondo blanco */
    }
    .form-container h2 {
        margin-bottom: 20px;
    }
    .form-container form {
        display: flex;
        flex-direction: column;
    }
    .form-container label {
        margin-bottom: 5px;
        font-weight: 600;
    }
    .form-container input, .form-container select {
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .form-container button {
        padding: 10px;
        border: none;
        border-radius: 5px;
        background-color: #333; /* negro */
        color: #fff; /* letras blancas */
        font-weight: 600;
        cursor: pointer;
    }
    .form-container button:hover {
        background-color: #666; /* gris oscuro */
    }
    .error-message {
        color: red;
        font-weight: 600;
    }
</style>
    <script>
        function toggleForm() {
            var formContainer = document.getElementById('form-container');
            var employeeTable = document.getElementById('employee-table');

            if (formContainer.style.display === 'none') {
                formContainer.style.display = 'block';
                employeeTable.style.display = 'none';
            } else {
                formContainer.style.display = 'none';
                employeeTable.style.display = 'table';
            }
        }

        function createUser(event) {
            event.preventDefault();

            var form = document.getElementById('create-user-form');
            var formData = new FormData(form);

            fetch('empleados.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Usuario creado con éxito');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function toggleEmployee(documento, estado) {
            var newState = estado === '1' ? '0' : '1';
            var formData = new FormData();
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
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function loadEmployeeData(documento, nombre, apellidos, contrasena, estado, cargo) {
            console.log(documento, nombre, apellidos, contrasena, estado, cargo);
            // Ocultar el título y la lista de empleados
            var title = document.querySelector('h1');
            var employeeTable = document.getElementById('employee-table');
            title.style.display = 'none';
            employeeTable.style.display = 'none';

            // Mostrar la sección de modificación
            var formContainer = document.getElementById('update-form-container');
            formContainer.style.display = 'block';

            // Llenar los campos del formulario
            var form  = document.getElementById('update-user-form');
            form.elements['documento'].value = documento;
            form.elements['nombre'].value = nombre;
            form.elements['apellidos'].value = apellidos;
            form.elements['contrasena'].value = contrasena;
            form.elements['update-estado'].checked = estado === '1';
            form.elements['cargo'].value = cargo;
        }

        function updateEmployee(event) {
            event.preventDefault();

            var confirmation = confirm("¿Estás seguro de que deseas modificar este usuario?");
            
            if (confirmation) {
                var form = document.getElementById('update-user-form');
                var formData = new FormData(form);

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
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    </script>
</head>
<body>
    <h1>Lista de Empleados</h1>
    <button class="btn-create-user" onclick="toggleForm()">Crear Usuario</button>
    <div id="form-container" class="form-container">
        <h2>Crear Nuevo Usuario</h2>
        <form id="create-user-form" onsubmit="createUser(event)">
            <input type="hidden" name="create_user" value="1">
            <label for="documento">Documento:</label>
            <input type="text" id="documento" name="documento" required>
            
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" required>
            
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
    <div id="update-form-container" class="form-container">
        <h2>Modificar Usuario</h2>
        <form id="update-user-form" onsubmit="updateEmployee(event)">
            <input type="hidden" name="update_employee" value="1">
            <label for="documento">Documento:</label>
            <input type="text" id="update-documento" name="documento" readonly>
            
            <label for="nombre">Nombre:</label>
            <input type="text" id="update-nombre" name="nombre" required>

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="update-apellidos" name="apellidos" required>
            
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="update-contrasena" name="contrasena" required>
            
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
                <th>Contraseña</th>
                <th>Estado</th>
                <th>Cargo</th>
                <th>Acciones</th> <!-- Nueva columna -->
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['emp_documento']); ?></td>
                    <td><?php echo htmlspecialchars($row['emp_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['emp_apellidos']); ?></td>
                    <td><?php echo htmlspecialchars($row['emp_contrasena']); ?></td>
                    <td><?php echo $row['emp_estado'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
                    <td><?php echo $row['emp_cargo'] == 1 ? 'Empleado' : 'Administrador'; ?></td>
                    <td>
                        <button onclick="toggleEmployee('<?php echo $row['emp_documento']; ?>', '<?php echo $row['emp_estado']; ?>')">
                            <?php echo $row['emp_estado'] == 1 ? 'Inactivar' : 'Activar'; ?>
                        </button>
                        <button onclick="loadEmployeeData('<?php echo $row['emp_documento']; ?>', '<?php echo $row['emp_nombre']; ?>', '<?php echo $row['emp_apellidos']; ?>', '<?php echo $row['emp_contrasena']; ?>', '<?php echo $row['emp_estado']; ?>', '<?php echo $row['emp_cargo']; ?>')">Modificar</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>