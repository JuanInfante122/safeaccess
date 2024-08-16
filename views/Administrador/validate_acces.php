<?php
// Recibe los datos enviados desde JavaScript
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody);

// Verifica si los datos esperados están presentes
if (isset($data->qrContent) && isset($data->areaId)) {
    $qrContent = $data->qrContent;
    $areaId = $data->areaId;

    // Realiza la verificación en la base de datos
    $servername = "localhost"; // Cambia esto según sea necesario
    $username = "root"; // Cambia esto según sea necesario
    $password = ""; // Cambia esto según sea necesario
    $dbname = "safeacces"; // Cambia esto según sea necesario

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM tbl_creacion_acceso WHERE acceso_empleado = '$qrContent' AND acceso_area = $areaId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // El empleado tiene acceso al área
        $response = array('accessGranted' => true);
    } else {
        // El empleado no tiene acceso al área
        $response = array('accessGranted' => false);
    }

    $conn->close();

    // Devuelve la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Datos no proporcionados, devuelve un error
    http_response_code(400);
    echo json_encode(array('error' => 'Bad request'));
}
?>
