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
        body, html {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: #000; /* letras negras */
            background-color: #fff; /* fondo blanco */
        }
        .area-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .area-table th, .area-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .area-table th {
            background-color: #eee; /* gris claro */
        }
        .btn-create-area {
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
        .btn-create-area:hover {
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
    var areaTable = document.getElementById('area-table');

    if (formContainer.style.display === 'none') {
        formContainer.style.display = 'block';
        areaTable.style.display = 'none';
    } else {
        formContainer.style.display = 'none';
        areaTable.style.display = 'table';
    }
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
    // Ocultar el título y la lista de ambientes
    var title = document.querySelector('h1');
    var areaTable = document.getElementById('area-table');
    title.style.display = 'none';
    areaTable.style.display = 'none';

    // Mostrar la sección de modificación
    var formContainer = document.getElementById('update-form-container');
    formContainer.style.display = 'block';

    // Llenar los campos del formulario
    var form  = document.getElementById('update-area-form');
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
                        <td><?php echo htmlspecialchars($row['area_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['area_aforo_max']); ?></td>
                        <td><?php echo htmlspecialchars($row['area_tipo']); ?></td>
                        <td><?php echo $row['area_estado'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
                        <td>
                            <button onclick="toggleArea('<?php echo $row['area_id']; ?>', '<?php echo $row['area_estado']; ?>')">
                                <?php echo $row['area_estado'] == 1 ? 'Inactivar' : 'Activar'; ?>
                            </button>
                            <button onclick="loadAreaData('<?php echo $row['area_id']; ?>', '<?php echo $row['area_nombre']; ?>', '<?php echo $row['area_aforo_max']; ?>', '<?php echo $row['area_tipo']; ?>', '<?php echo $row['area_estado']; ?>')">Modificar</button>
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
</body>
</html>
