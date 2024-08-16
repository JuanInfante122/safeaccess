<?php
// loginModel.php
require_once('config/db.php'); // Aquí debes tener tu script de conexión a la base de datos

class LoginModel {
    public function validateCredentials($username, $password) {
        $db = new Database();
        $conn = $db->connect();

        $stmt = $conn->prepare("SELECT * FROM tbl_empleado WHERE emp_documento = ? AND emp_contrasena = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return true; // Inicio de sesión exitoso
        } else {
            return false; // Usuario o contraseña incorrectos
        }
    }
}
?>
