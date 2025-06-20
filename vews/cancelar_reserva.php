<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost","root","","turismo");
if ($conn->connect_error) die("Error BD: ".$conn->connect_error);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Reserva no válida.");
}
$id_reserva = intval($_GET['id']);

$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $user_input = trim($_POST['usuario'] ?? '');
    $pass_input = $_POST['password'] ?? '';

    // Verificar que el usuario coincide con la sesión
    if ($user_input !== $_SESSION['usuario']) {
        $error = "Usuario incorrecto.";
    } else {
        // Obtener hash de la BD
        $stmt = $conn->prepare("SELECT password FROM Usuarios WHERE id_usuario=?");
        $stmt->bind_param("i", $_SESSION['id_usuario']);
        $stmt->execute();
        $hash = $stmt->get_result()->fetch_assoc()['password'];
        $stmt->close();

        if (!password_verify($pass_input, $hash)) {
            $error = "Contraseña incorrecta.";
        } else {
            // Borrar la reserva
            $del = $conn->prepare("DELETE FROM Reservas WHERE id_reserva=? AND id_usuario=?");
            $del->bind_param("ii", $id_reserva, $_SESSION['id_usuario']);
            $del->execute();
            $del->close();
            $conn->close();
            header("Location: mis_reservas.php");
            exit;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Confirmar Cancelación</title>
  <style>
    body{font-family:Arial;padding:40px;max-width:400px;margin:auto;background:#f7f7f7;}
    form{background:white;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
    label{display:block;margin-top:15px;}
    input{width:100%;padding:8px;margin-top:5px;box-sizing:border-box;}
    .error{color:red;margin-top:10px;}
    button{margin-top:20px;padding:10px;width:100%;background:#dc3545;color:white;border:none;border-radius:5px;cursor:pointer;}
    button:hover{background:#c82333;}
    a{display:block;text-align:center;margin-top:15px;color:#007BFF;text-decoration:none;}
  </style>
</head>
<body>

<h2>Cancelar Reserva #<?= $id_reserva ?></h2>

<?php if($error): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
  <label for="usuario">Usuario:</label>
  <input type="text" id="usuario" name="usuario" required>

  <label for="password">Contraseña:</label>
  <input type="password" id="password" name="password" required>

  <button type="submit">Confirmar Cancelación</button>
</form>

<a href="mis_reservas.php">&larr; Volver a Mis Reservas</a>

</body>
</html>
