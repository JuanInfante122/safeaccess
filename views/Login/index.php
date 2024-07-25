<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="../../assets/LogInStyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <div class="left-box">
                <h1>Bienvenido</h1>
                <p>Acceda a su cuenta para gestionar la seguridad y el control de accesos de manera eficiente.</p>
            </div>
            <div class="right-box">
                <h2>Iniciar Sesión</h2>
                <form id="loginForm" action="loginValidation.php" method="POST">
                    <div class="user-box">
                        <input type="text" name="username" required>
                        <label>Usuario</label>
                    </div>
                    <div class="user-box">
                        <input type="password" name="password" required>
                        <label>Contraseña</label>
                    </div>
                    <button type="submit" class="btn-login">Ingresar</button>
                    <a href="#" class="forgot-password">Recuperar Contraseña</a>
                </form>
            </div>
        </div>
    </div>
    <footer>
        &copy; 2024 Safe Access. Todos los derechos reservados.
    </footer>

</body>
</html>
