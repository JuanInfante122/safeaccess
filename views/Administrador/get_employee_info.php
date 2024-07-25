<?php
// Recibe los datos enviados desde JavaScript
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody);

// Verifica si los datos esperados están presentes
if (isset($data->qrContent)) {
    $qrContent = $data->qrContent;

    // Conexión a la base de datos
    $servername = "localhost"; // Cambia esto según sea necesario
    $username = "root"; // Cambia esto según sea necesario
    $password = ""; // Cambia esto según sea necesario
    $dbname = "safeacces"; // Cambia esto según sea necesario

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Consulta SQL para obtener la información del empleado
    $sql = "SELECT emp_nombre, emp_apellidos FROM tbl_empleado WHERE emp_documento = '$qrContent'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Obtiene la fila de resultado como un array asociativo
        $row = $result->fetch_assoc();

        // Almacena los datos del empleado en un array
        $employeeData = array(
            'nombre' => $row['emp_nombre'],
            'apellidos' => $row['emp_apellidos']
        );

        // Devuelve la respuesta en formato JSON con la información del empleado
        header('Content-Type: application/json');
        echo json_encode($employeeData);
    } else {
        // El empleado no fue encontrado en la base de datos
        http_response_code(404);
        echo json_encode(array('error' => 'Empleado no encontrado'));
    }

    $conn->close();
} else {
    // Datos no proporcionados, devuelve un error
    http_response_code(400);
    echo json_encode(array('error' => 'Solicitud incorrecta'));
}
?>
