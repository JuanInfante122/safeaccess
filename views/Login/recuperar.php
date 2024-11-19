<?php

use PHPMailer\PHPMailer\PHPMailer;

session_start();
require '../../vendor/autoload.php';

$conn = new mysqli('localhost', 'root', '', 'safeacces');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Variables para manejar pasos
$step = isset($_POST['step']) ? $_POST['step'] : 1;
$message = "";

// Lógica del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1: // Paso 1: Enviar código al correo
            $documento = $_POST['username'];

            // Verificar si el usuario existe
            $query = "SELECT empleado_correo FROM tbl_empleado WHERE emp_documento = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $documento);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $correo = $row['empleado_correo'];

                // Generar código
                $codigo = rand(100000, 999999);

                // Enviar correo con PHPMailer
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'cloudmyl121@gmail.com';
                $mail->Password = 'ycbpgbtljtoppuqi';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('cloudmyl121@gmail.com', 'Recuperación de Contraseña');
                $mail->addAddress($correo);
                $mail->isHTML(true); // Habilitar HTML
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Código de Verificación - SafeAccess';
                $mail->Body = '
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 0;
                            background-color: #f4f4f4;
                            color: #333;
                        }
                        .container {
                            max-width: 600px;
                            margin: 20px auto;
                            background: #ffffff;
                            padding: 20px;
                            border-radius: 8px;
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        }
                        .header {
                            background-color: #2c3e50;
                            color: #ffffff;
                            padding: 10px 20px;
                            border-radius: 8px 8px 0 0;
                            text-align: center;
                        }
                        .header h1 {
                            margin: 0;
                            font-size: 24px;
                        }
                        .content {
                            padding: 20px;
                            text-align: center;
                        }
                        .content p {
                            font-size: 16px;
                            margin-bottom: 20px;
                        }
                        .content .code {
                            font-size: 24px;
                            font-weight: bold;
                            color: #2c3e50;
                            margin: 20px 0;
                        }
                        .footer {
                            margin-top: 20px;
                            text-align: center;
                            font-size: 14px;
                            color: #777;
                        }
                        .footer a {
                            color: #2c3e50;
                            text-decoration: none;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>SafeAccess</h1>
                        </div>
                        <div class="content">
                            <p>Hola,</p>
                            <p>Recibimos una solicitud para recuperar la contraseña asociada a tu cuenta de SafeAccess.</p>
                            <p>Por favor, utiliza el siguiente código para completar el proceso de recuperación de contraseña:</p>
                            <div class="code">' . $codigo . '</div>
                            <p>Este código es válido durante los próximos 10 minutos. Si no solicitaste este código, por favor ignora este correo.</p>
                        </div>
                        <div class="footer">
                            <p>¿Necesitas ayuda? <a href="mailto:soporte@safeaccess.com">Contáctanos</a></p>
                            <p>&copy; 2024 SafeAccess. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </body>
                </html>';

                if ($mail->send()) {
                    $_SESSION['codigo'] = $codigo;
                    $_SESSION['documento'] = $documento;
                    $step = 2;
                } else {
                    $message = "Error al enviar el correo. Inténtelo de nuevo.";
                }
            } else {
                $message = "Usuario no encontrado.";
            }
            break;

        case 2: // Paso 2: Validar código
            $codigoIngresado = $_POST['code'];
            if ($codigoIngresado == $_SESSION['codigo']) {
                $step = 3;
            } else {
                $message = "Código incorrecto. Inténtelo de nuevo.";
            }
            break;

        case 3: // Paso 3: Cambiar contraseña
            $nuevoPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            // Validar seguridad de la contraseña
            if ($nuevoPassword !== $confirmPassword) {
                $message = "Las contraseñas no coinciden.";
            } elseif (strlen($nuevoPassword) < 8 || !preg_match("/[A-Z]/", $nuevoPassword) || !preg_match("/[0-9]/", $nuevoPassword)) {
                $message = "La contraseña debe tener al menos 8 caracteres, una letra mayúscula y un número.";
            } else {
                $hashPassword = password_hash($nuevoPassword, PASSWORD_BCRYPT);

                // Actualizar contraseña
                $documento = $_SESSION['documento'];
                $query = "UPDATE tbl_empleado SET emp_contrasena = ? WHERE emp_documento = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("si", $hashPassword, $documento);

                if ($stmt->execute()) {
                    $message = "Contraseña actualizada correctamente.";
                    session_destroy();
                    $step = 1;
                } else {
                    $message = "Error al actualizar la contraseña. Inténtelo más tarde.";
                }
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos generales */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #333, #444); color: #fff; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .container { background: #fff; color: #333; width: 100%; max-width: 900px; display: flex; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); overflow: hidden; }
        .left-box { background: #2c2c2c; color: #fff; width: 50%; padding: 40px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
        .right-box { background: #f9f9f9; width: 50%; padding: 40px; display: flex; flex-direction: column; justify-content: center; border-left: 1px solid #ddd; }
        h1, h2 { margin-bottom: 20px; }
        .user-box { position: relative; margin-bottom: 25px; }
        .user-box input { width: 100%; padding: 12px; font-size: 16px; border: none; border-bottom: 2px solid #777; outline: none; transition: border-color 0.3s; }
        .btn-login { padding: 15px; font-size: 16px; background: #333; color: #fff; border: none; border-radius: 8px; cursor: pointer; transition: background 0.3s; }
        .btn-login:hover { background: #555; }
        .forgot-password { display: block; text-align: center; margin-top: 15px; font-size: 14px; color: #333; text-decoration: none; }
        .forgot-password:hover { text-decoration: underline; }
        .message { margin: 10px 0; font-size: 14px; color: red; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-box">
            <h1>Recuperar Contraseña</h1>
            <p>Reciba un código de verificación para cambiar su contraseña.</p>
        </div>
        <div class="right-box">
            <h2>Recuperar Contraseña</h2>
            <!-- Mostrar mensajes de error si existen -->
            <?php if (!empty($message)): ?>
                <div class="message"><?= $message ?></div>
            <?php endif; ?>
            <!-- Formulario dinámico por pasos -->
            <form method="POST">
                <?php if ($step == 1): ?>
                    <div class="user-box">
                        <input type="text" name="username" required>
                        <label>Documento</label>
                    </div>
                    <button type="submit" name="step" value="1" class="btn-login">Enviar Código</button>
                    <a href="../Login/index.php" class="forgot-password">Volver a Iniciar Sesión</a>
                <?php elseif ($step == 2): ?>
                    <div class="user-box">
                        <input type="text" name="code" required>
                        <label>Código de Verificación</label>
                    </div>
                    <button type="submit" name="step" value="2" class="btn-login">Verificar Código</button>
                <?php elseif ($step == 3): ?>
                    <div class="user-box">
                        <input type="password" name="new_password" required>
                        <label>Nueva Contraseña</label>
                    </div>
                    <div class="user-box">
                        <input type="password" name="confirm_password" required>
                        <label>Confirmar Contraseña</label>
                    </div>
                    <button type="submit" name="step" value="3" class="btn-login">Cambiar Contraseña</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
