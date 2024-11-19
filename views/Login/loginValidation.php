<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Conexión a la base de datos
    $db_host = "localhost";
    $db_user = "root";
    $db_password = "";
    $db_name = "safeacces";

    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta para obtener los datos del usuario
    $sql = "SELECT * FROM tbl_empleado WHERE emp_documento = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        // Obtener la fila de resultado como un array asociativo
        $row = $result->fetch_assoc();
        
        // Verificar la contraseña usando password_verify
        if (password_verify($password, $row['emp_contrasena'])) {
            // Contraseña correcta
            // Verificar el cargo del empleado
            if ($row['emp_cargo'] == 1) {
                // Si el cargo es 1, el empleado es usuario, redireccionar a la vista de empleados
                $_SESSION["username"] = $username;
                header("Location: ../Empleados/index.php");
                exit;
            } elseif ($row['emp_cargo'] == 2) {
                // Si el cargo es 2, el empleado es administrador, redireccionar a la vista de administradores
                $_SESSION["username"] = $username;
                header("Location: ../Administrador/index.php");
                exit;
            } else {
                // Cargo no reconocido
                header("Location: index.php?error=cargo");
                exit;
            }
        } else {
            // Contraseña incorrecta
            header("Location: index.php?error=1");
            exit;
        }
    } else {
        // Usuario no encontrado
        header("Location: index.php?error=2");
        exit;
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>
