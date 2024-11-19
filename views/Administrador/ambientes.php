<?php
require_once('../../config/db.php');
session_start();

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION["username"];
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create_area"])) {
        // Código para crear un nuevo ambiente
        $nombre = $_POST["nombre"];
        $aforo_max = $_POST["aforo_max"];
        $tipo = $_POST["tipo"];

        try {
            // Conectar a la base de datos
            $db = Database::connect();

            // Preparar la consulta SQL para insertar el nuevo ambiente
            $query = "INSERT INTO tbl_area (area_nombre, area_aforo_max, area_tipo, area_estado) VALUES (?, ?, ?, 1)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("sis", $nombre, $aforo_max, $tipo);
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

    } elseif (isset($_POST["toggle_area"])) {
        // Código para activar/inactivar el ambiente
        $area_id = $_POST["area_id"];
        $estado = $_POST["estado"];

        try {
            // Conectar a la base de datos
            $db = Database::connect();

            // Preparar la consulta SQL para actualizar el estado del ambiente
            $query = "UPDATE tbl_area SET area_estado = ? WHERE area_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $estado, $area_id);
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
    } elseif (isset($_POST["update_area"])) {
        // Código para modificar el ambiente
        $area_id = $_POST["area_id"];
        $nombre = $_POST["nombre"];
        $aforo_max = $_POST["aforo_max"];
        $tipo = $_POST["tipo"];
        $estado = isset($_POST["estado"]) ? 1 : 0;

        try {
            // Conectar a la base de datos
            $db = Database::connect();

            // Preparar la consulta SQL para actualizar los datos del ambiente
            $query = "UPDATE tbl_area SET area_nombre = ?, area_aforo_max = ?, area_tipo = ?, area_estado = ? WHERE area_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("sisii", $nombre, $aforo_max, $tipo, $estado, $area_id);
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

    // Preparar la consulta SQL para obtener los ambientes
    $query = "SELECT area_id, area_nombre, area_aforo_max, area_tipo, area_estado FROM tbl_area";
    $result = $db->query($query);

    // Cerrar la conexión a la base de datos
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
    <title>Lista de Ambientes</title>
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
        .btn-create-area {
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
        .btn-create-area:hover {
            background-color: #74b9ff;
        }

        /* Table Styles for Desktop */
        .area-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: table;
        }
        .area-table th, .area-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 15px;
            color: #2d3436;
        }
        .area-table th {
            background-color: #dfe6e9;
            font-weight: 600;
        }
        .area-table tbody tr:hover {
            background-color: #f1f3f5;
        }
        .area-table td:last-child {
            text-align: center;
        }
        .area-table button {
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
        .area-table .btn-toggle {
            background-color: #636e72;
        }
        .area-table .btn-toggle:hover {
            background-color: #b2bec3;
        }
        .area-table .btn-modify {
            background-color: #00b894;
        }
        .area-table .btn-modify:hover {
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
            .btn-create-area {
                font-size: 14px;
                padding: 8px 15px;
            }
            .area-table, .area-table thead, .area-table tbody, .area-table th, .area-table td, .area-table tr {
                display: block;
                width: 100%;
            }
            .area-table thead {
                display: none;
            }
            .area-table tbody tr {
                margin-bottom: 15px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                padding: 15px;
                background: #fff;
            }
            .area-table td {
                display: flex;
                justify-content: space-between;
                padding: 10px;
                border-bottom: none;
                font-size: 14px;
            }
            .area-table td:before {
                content: attr(data-label);
                font-weight: 600;
                color: #2d3436;
                flex-basis: 40%;
                text-align: left;
            }
            .area-table button {
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
            var formContainer = document.getElementById('form-container');
            var areaTable = document.getElementById('area-table');
            formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
            areaTable.style.display = formContainer.style.display === 'none' ? 'table' : 'none';
        }

        function createArea(event) {
            event.preventDefault();
            var form = document.getElementById('create-area-form');
            var formData = new FormData(form);

            fetch('ambientes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Ambiente creado con éxito');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function toggleArea(area_id, estado) {
            var newState = estado === '1' ? '0' : '1';
            var formData = new FormData();
            formData.append('toggle_area', '1');
            formData.append('area_id', area_id);
            formData.append('estado', newState);

            fetch('ambientes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Estado del ambiente actualizado');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function loadAreaData(area_id, nombre, aforo_max, tipo, estado) {
            var title = document.querySelector('h1');
            var areaTable = document.getElementById('area-table');
            title.style.display = 'none';
            areaTable.style.display = 'none';

            var formContainer = document.getElementById('update-form-container');
            formContainer.style.display = 'block';

            var form = document.getElementById('update-area-form');
            form.elements['area_id'].value = area_id;
            form.elements['nombre'].value = nombre;
            form.elements['aforo_max'].value = aforo_max;
            form.elements['tipo'].value = tipo;
            form.elements['estado'].checked = estado === '1';
        }

        function updateArea(event) {
            event.preventDefault();
            var form = document.getElementById('update-area-form');
            var formData = new FormData(form);

            fetch('ambientes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Ambiente actualizado con éxito');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Lista de Ambientes</h1>
        <button class="btn-create-area" onclick="toggleForm()">Crear Ambiente</button>
        
        <div id="form-container" class="form-container">
            <h2>Crear Nuevo Ambiente</h2>
            <form id="create-area-form" onsubmit="createArea(event)">
                <input type="hidden" name="create_area" value="1">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                <label for="aforo_max">Aforo Máximo:</label>
                <input type="number" id="aforo_max" name="aforo_max" required>
                <label for="tipo">Tipo:</label>
                <input type="text" id="tipo" name="tipo" required>
                <button type="submit">Crear Ambiente</button>
            </form>
        </div>

        <div id="update-form-container" class="form-container">
            <h2>Modificar Ambiente</h2>
            <form id="update-area-form" onsubmit="updateArea(event)">
                <input type="hidden" name="update_area" value="1">
                <input type="hidden" id="area_id" name="area_id">
                <label for="nombre">Nombre:</label>
                <input type="text" id="update-nombre" name="nombre" required>
                <label for="aforo_max">Aforo Máximo:</label>
                <input type="number" id="update-aforo_max" name="aforo_max" required>
                <label for="tipo">Tipo:</label>
                <input type="text" id="update-tipo" name="tipo" required>
                <label for="estado">Estado:</label>
                <input type="checkbox" id="update-estado" name="estado">
                <button type="submit">Modificar Ambiente</button>
            </form>
        </div>

        <table id="area-table" class="area-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Aforo Máximo</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Nombre"><?php echo htmlspecialchars($row['area_nombre']); ?></td>
                            <td data-label="Aforo Máximo"><?php echo htmlspecialchars($row['area_aforo_max']); ?></td>
                            <td data-label="Tipo"><?php echo htmlspecialchars($row['area_tipo']); ?></td>
                            <td data-label="Estado"><?php echo $row['area_estado'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
                            <td>
                                <button class="btn-toggle" onclick="toggleArea('<?php echo $row['area_id']; ?>', '<?php echo $row['area_estado']; ?>')">
                                    <?php echo $row['area_estado'] == 1 ? 'Inactivar' : 'Activar'; ?>
                                </button>
                                <button class="btn-modify" onclick="loadAreaData('<?php echo $row['area_id']; ?>', '<?php echo $row['area_nombre']; ?>', '<?php echo $row['area_aforo_max']; ?>', '<?php echo $row['area_tipo']; ?>', '<?php echo $row['area_estado']; ?>')">Modificar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No se encontraron ambientes.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
