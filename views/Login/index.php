<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* General Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #333, #444);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        /* Container */
        .container {
            background: #fff;
            color: #333;
            width: 100%;
            max-width: 900px;
            display: flex;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        /* Left Box */
        .left-box {
            background: #2c2c2c;
            color: #fff;
            width: 50%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .left-box h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .left-box p {
            font-size: 16px;
            line-height: 1.6;
            max-width: 300px;
        }

        /* Right Box */
        .right-box {
            background: #f9f9f9;
            width: 50%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-left: 1px solid #ddd;
        }
        .right-box h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #333;
            text-align: center;
        }

        .user-box {
            position: relative;
            margin-bottom: 25px;
        }
        .user-box input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            color: #333;
            background: transparent;
            border: none;
            border-bottom: 2px solid #777;
            outline: none;
            transition: border-color 0.3s;
        }
        .user-box input:focus {
            border-color: #333;
        }
        .user-box label {
            position: absolute;
            top: 10px;
            left: 0;
            font-size: 16px;
            color: #777;
            pointer-events: none;
            transition: 0.3s;
        }
        .user-box input:focus + label,
        .user-box input:valid + label {
            top: -18px;
            font-size: 14px;
            color: #333;
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            background-color: #333;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        .btn-login:hover {
            background-color: #555;
        }

        /* Forgot Password Link */
        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #555;
            text-decoration: none;
            transition: color 0.3s;
        }
        .forgot-password:hover {
            color: #333;
            text-decoration: underline;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #ddd;
            width: 100%;
            position: fixed;
            bottom: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                max-width: 100%;
            }
            .left-box, .right-box {
                width: 100%;
            }
            .left-box {
                padding: 20px;
            }
            .right-box {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
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
                <a href="recuperar.php" class="forgot-password">Recuperar Contraseña</a>
            </form>
        </div>
    </div>
    <footer>
        &copy; 2024 Safe Access. Todos los derechos reservados.
    </footer>
</body>
</html>
