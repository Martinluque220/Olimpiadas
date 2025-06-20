<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$db = "turismo";
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $pass_confirm = $_POST['password_confirm'] ?? '';

    if (!$usuario || !$email || !$pass) {
        $mensaje = "Completa todos los campos.";
    } elseif ($pass !== $pass_confirm) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        // Verificar si usuario o email ya existen
        $sql = "SELECT id_usuario FROM Usuarios WHERE usuario = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $usuario, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = "Usuario o email ya están registrados.";
        } else {
            $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);
            $sqlInsert = "INSERT INTO Usuarios (usuario, email, password) VALUES (?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("sss", $usuario, $email, $pass_hashed);
            if ($stmtInsert->execute()) {
                $_SESSION['usuario'] = $usuario;
                header("Location: index.php");
                exit;
            } else {
                $mensaje = "Error al registrar usuario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <style>
        body { font-family: Arial; padding: 30px; max-width: 400px; margin: auto; }
        label { display: block; margin-top: 15px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        .error { color: red; margin-top: 10px; }
        button { margin-top: 20px; padding: 10px; width: 100%; background: #007BFF; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        a { display: block; margin-top: 15px; text-align: center; }
    </style>
</head>
<body>
    <h2>Registro de Usuario</h2>
    <?php if ($mensaje) echo "<p class='error'>$mensaje</p>"; ?>
    <form method="post" action="">
        <label>Usuario:
            <input type="text" name="usuario" required>
        </label>
        <label>Email:
            <input type="email" name="email" required>
        </label>
        <label>Contraseña:
            <input type="password" name="password" required>
        </label>
        <label>Confirmar Contraseña:
            <input type="password" name="password_confirm" required>
        </label>
        <button type="submit">Registrar</button>
    </form>
    <a href="login.php">¿Ya tienes cuenta? Iniciar sesión</a>
</body>
</html>
