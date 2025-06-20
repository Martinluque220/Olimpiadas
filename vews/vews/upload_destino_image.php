<?php
session_start();
// (opcional) validar que seas administrador o usuario logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$host = "localhost"; $user = "root"; $pass = ""; $db = "turismo";
$conn = new mysqli($host,$user,$pass,$db);
if ($conn->connect_error) die("DB error: ".$conn->connect_error);

$message = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_destino = intval($_POST['id_destino'] ?? 0);
    if ($id_destino <= 0 || !isset($_FILES['imagen'])) {
        $message = "Selecciona un destino y un archivo.";
    } else {
        $file     = $_FILES['imagen'];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['png','jpg','jpeg','gif'];
        if ($file['error'] !== UPLOAD_ERR_OK || !in_array($ext, $allowed)) {
            $message = "Error al subir o formato no permitido.";
        } else {
            // Directorio donde guardas las imágenes
            $dir = __DIR__ . '/assets/images/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            // Nombre único para evitar colisiones
            $newName = "destino_{$id_destino}." . $ext;
            $destPath = $dir . $newName;

            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                // Guardar en BD
                $stmt = $conn->prepare("UPDATE Destinos SET imagen = ? WHERE id_destino = ?");
                $stmt->bind_param("si", $newName, $id_destino);
                if ($stmt->execute()) {
                    $message = "Imagen subida con éxito.";
                } else {
                    $message = "Error al actualizar BD.";
                }
                $stmt->close();
            } else {
                $message = "No se pudo mover el archivo.";
            }
        }
    }
}

// Obtener lista de destinos
$res = $conn->query("SELECT id_destino, ciudad, pais, imagen FROM Destinos ORDER BY id_destino");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Subir Imagen de Destino</title>
  <style>
    body { font-family:Arial; max-width:600px; margin:40px auto; padding:20px; }
    label, select, input { display:block; width:100%; margin-top:10px; }
    input[type="file"] { padding:4px; }
    button { margin-top:20px; padding:10px 20px; background:#007BFF; color:#fff; border:none; border-radius:5px; cursor:pointer;}
    button:hover { background:#0056b3; }
    .message { margin-top:20px; color:green; }
    .thumb { max-height:80px; margin-top:10px; }
  </style>
</head>
<body>

  <h1>Subir/Actualizar Imagen de Destino</h1>
  <?php if ($message): ?>
    <p class="message"><?=htmlspecialchars($message)?></p>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <label for="id_destino">Destino:</label>
    <select name="id_destino" id="id_destino" required>
      <option value="">-- Selecciona --</option>
      <?php while($d = $res->fetch_assoc()): ?>
        <option value="<?=$d['id_destino']?>">
          <?=$d['ciudad']?>, <?=$d['pais']?>
          <?php if ($d['imagen']): ?> (ya: <?=$d['imagen']?>)<?php endif; ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label for="imagen">Imagen (.png .jpg .gif):</label>
    <input type="file" name="imagen" id="imagen" accept=".png,.jpg,.jpeg,.gif" required>

    <button type="submit">Subir Imagen</button>
  </form>

</body>
</html>
