<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Conexión
$conn = new mysqli("localhost", "root", "", "turismo");
if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

// Validar ID de paquete
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de paquete no válido.");
}
$id_paquete = intval($_GET['id']);

// Obtener datos del paquete
$stmt = $conn->prepare("
  SELECT p.*, d.ciudad AS destino_ciudad, d.pais AS destino_pais
  FROM Paquetes p
  JOIN Destinos d ON p.id_destino=d.id_destino
  WHERE p.id_paquete=?
");
$stmt->bind_param("i", $id_paquete);
$stmt->execute();
$paquete = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener guías
$guias = $conn->query("SELECT * FROM PaquetesGuia");
// Obtener habitaciones
$habs  = $conn->query("SELECT * FROM TiposHabitacion");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reservar: <?=htmlspecialchars($paquete['nombre_paquete'])?></title>
  <style>
    body{font-family:Arial;max-width:700px;margin:40px auto;background:#fafafa;padding:20px;border-radius:8px;}
    h1{color:#007BFF} label{display:block;margin-top:15px;font-weight:bold}
    input, select{width:100%;padding:8px;margin-top:5px}
    button{margin-top:20px;padding:10px 20px;background:#28a745;color:#fff;border:none;border-radius:5px;cursor:pointer}
    button:hover{background:#218838} a{color:#007BFF;text-decoration:none}
  </style>
</head>
<body>

<a href="index.php">&larr; Volver a Paquetes</a>
<h1>Reservar: <?=htmlspecialchars($paquete['nombre_paquete'])?></h1>

<form method="post" action="confirmar_reserva.php?id=<?=$id_paquete?>">
  <label for="duracion">Duración (días):</label>
  <input type="number" id="duracion" name="duracion" value="<?=$paquete['duracion_dias']?>" min="1" required>

  <label>Paquete de Guía Turístico:</label>
  <?php while($g = $guias->fetch_assoc()): ?>
    <div>
      <input type="radio" name="paquete_guia" id="guia<?=$g['id_paquete_guia']?>" value="<?=$g['id_paquete_guia']?>" required>
      <label for="guia<?=$g['id_paquete_guia']?>">
        <?=$g['nombre_paquete_guia']?> – $<?=number_format($g['precio'],2)?>
      </label>
    </div>
  <?php endwhile ?>

  <label for="cantidad_autos">Autos a alquilar (por día):</label>
  <input type="number" id="cantidad_autos" name="cantidad_autos" value="0" min="0">

  <label for="tipo_habitacion">Tipo de habitación:</label>
  <select id="tipo_habitacion" name="tipo_habitacion" required>
    <?php while($h = $habs->fetch_assoc()): ?>
      <option value="<?=$h['id_tipo_habitacion']?>">
        <?=$h['nombre_tipo']?> – $<?=number_format($h['precio'],2)?>
      </option>
    <?php endwhile ?>
  </select>

  <button type="submit">Siguiente: Confirmar</button>
</form>

</body>
</html>
<?php $conn->close(); ?>
