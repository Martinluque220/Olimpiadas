<?php
session_start();

// Si ya está logueado, ir al index
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$db = "turismo";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = '';

// Procesar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $pass    = $_POST['password'] ?? '';

    // Validaciones básicas
    if ($usuario === '' || $pass === '') {
        $mensaje = "Por favor completa todos los campos.";
    } else {
        // Buscar usuario en la tabla
        $stmt = $conn->prepare("SELECT id_usuario, password FROM Usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            // Verificar contraseña
            if (password_verify($pass, $row['password'])) {
                // Credenciales correctas: guardar sesión
                $_SESSION['id_usuario'] = $row['id_usuario'];
                $_SESSION['usuario']    = $usuario;
                // Redirigir al index
                header("Location: index.php");
                exit;
            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        } else {
            $mensaje = "Usuario no encontrado.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión</title>
  <style>
    body { font-family: Arial; padding: 40px; background: #f7f7f7; max-width: 400px; margin: auto; }
    h2 { text-align: center; }
    form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    label { display: block; margin-top: 15px; }
    input { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
    .error { color: red; margin-top: 10px; text-align: center; }
    button { margin-top: 20px; width: 100%; padding: 10px; background: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #0056b3; }
    .links { text-align: center; margin-top: 15px; }
    .links a { color: #007BFF; text-decoration: none; }
    .links a:hover { text-decoration: underline; }
  </style>
</head>
<body>

<h2>Iniciar Sesión</h2>

<?php if ($mensaje): ?>
  <p class="error"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

<form method="post" action="">
  <label for="usuario">Usuario</label>
  <input type="text" id="usuario" name="usuario" required>

  <label for="password">Contraseña</label>
  <input type="password" id="password" name="password" required>

  <button type="submit">Entrar</button>
</form>

<div class="links">
  <a href="registro.php">¿No tienes cuenta? Regístrate</a>
</div>

</body>
</html>
