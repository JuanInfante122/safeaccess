<?php
// Recibe los datos enviados desde JavaScript
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody);

// Verifica si los datos esperados están presentes
if (isset($data->qrContent) && isset($data->areaId)) {
    $qrContent = $data->qrContent;
    $areaId = $data->areaId;

    // Conexión a la base de datos
    $servername = "localhost"; // Cambia según sea necesario
    $username = "root"; // Cambia según sea necesario
    $password = ""; // Cambia según sea necesario
    $dbname = "safeacces"; // Cambia según sea necesario

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Verificar si el empleado tiene acceso al área
    $sql = "SELECT * FROM tbl_creacion_acceso WHERE acceso_empleado = '$qrContent' AND acceso_area = $areaId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // El empleado tiene acceso al área
        $response = array('accessGranted' => true);

        // Verificar si ya existe un registro sin salida
        $checkSql = "SELECT * FROM tbl_registro_accesos 
                     WHERE acceso_empleado = '$qrContent' 
                     AND acceso_area = $areaId 
                     AND hora_salida IS NULL";
        $checkResult = $conn->query($checkSql);

        if ($checkResult->num_rows > 0) {
            // Actualizar el registro con la hora de salida
            $updateSql = "UPDATE tbl_registro_accesos 
                          SET hora_salida = CURRENT_TIMESTAMP 
                          WHERE acceso_empleado = '$qrContent' 
                          AND acceso_area = $areaId 
                          AND hora_salida IS NULL";
            if (!$conn->query($updateSql)) {
                error_log("Error al actualizar la hora de salida: " . $conn->error);
            } else {
                $response['action'] = 'exitRecorded';
            }
        } else {
            // Crear un nuevo registro con la hora de entrada
            $insertSql = "INSERT INTO tbl_registro_accesos (acceso_empleado, acceso_area, fecha_acceso) 
                          VALUES ('$qrContent', $areaId, CURRENT_TIMESTAMP)";
            if (!$conn->query($insertSql)) {
                error_log("Error al registrar la entrada: " . $conn->error);
            } else {
                $response['action'] = 'entryRecorded';
            }
        }
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
