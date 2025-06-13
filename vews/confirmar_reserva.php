<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de paquete no válido.");
}
$id_paquete = intval($_GET['id']);

// Recibir POST
$duracion      = intval($_POST['duracion'] ?? 0);
$id_guia       = intval($_POST['paquete_guia'] ?? 0);
$cantidad_autos= intval($_POST['cantidad_autos'] ?? 0);
$id_habitacion = intval($_POST['tipo_habitacion'] ?? 0);

$conn = new mysqli("localhost", "root", "", "turismo");
if ($conn->connect_error) die("Conexión fallida: ".$conn->connect_error);

// Traer precio base del paquete
$stmt = $conn->prepare("SELECT precio_base, nombre_paquete FROM Paquetes WHERE id_paquete=?");
$stmt->bind_param("i",$id_paquete);
$stmt->execute();
$pkg = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Traer precio guía
$stmt = $conn->prepare("SELECT precio,nombre_paquete_guia FROM PaquetesGuia WHERE id_paquete_guia=?");
$stmt->bind_param("i",$id_guia);
$stmt->execute();
$g = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Traer precio habitación
$stmt = $conn->prepare("SELECT precio,nombre_tipo FROM TiposHabitacion WHERE id_tipo_habitacion=?");
$stmt->bind_param("i",$id_habitacion);
$stmt->execute();
$h = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Calcular totales
$precio_total = $pkg['precio_base'];
$precio_total += $g['precio'];
$precio_total += $h['precio'];
$precio_autos = 50 * $cantidad_autos * $duracion;
$precio_total += $precio_autos;

// Fechas
$inicio = date('Y-m-d');
$fin    = date('Y-m-d', strtotime("+$duracion days"));

// Datos usuario
$id_usuario    = $_SESSION['id_usuario'];
$nombre_cliente= $_SESSION['nombre'] ?? '';
$email_cliente = $_SESSION['email']  ?? '';

// Insertar reserva
$sql = "INSERT INTO Reservas (
    id_cliente, id_paquete, nombre_cliente, email_cliente,
    fecha_viaje_inicio, fecha_viaje_fin, estado_reserva,
    precio_total_reserva, numero_pasajeros, duracion_dias,
    id_paquete_guia, cantidad_autos, id_tipo_habitacion, id_usuario
) VALUES (
    ?,?,?,?,?,?,'Confirmada',?,?,?,?,?,?
)";
$stmt = $conn->prepare($sql);
$id_cliente = 0; // si manejas tabla Clientes puedes setearlo
$npasaj = 1;
$stmt->bind_param(
    "iissssdiisiii",
    $id_cliente, $id_paquete, $nombre_cliente, $email_cliente,
    $inicio, $fin, $precio_total, $npasaj, $duracion,
    $id_guia, $cantidad_autos, $id_habitacion, $id_usuario
);
$stmt->execute();
$id_reserva = $stmt->insert_id;
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reserva Confirmada</title>
  <style>
    body{font-family:Arial;max-width:600px;margin:40px auto;padding:20px;background:#fafafa;border-radius:8px}
    h1{color:#28a745} table{width:100%;border-collapse:collapse;margin-top:20px}
    th,td{padding:8px;border:1px solid #ddd;text-align:left}
    a{display:inline-block;margin-top:20px;color:#007BFF;text-decoration:none}
  </style>
</head>
<body>

<h1>✓ Reserva Confirmada</h1>
<p>Tu reserva (ID <strong><?=$id_reserva?></strong>) ha sido creada:</p>

<table>
  <tr><th>Paquete</th> <td><?=htmlspecialchars($pkg['nombre_paquete'])?></td></tr>
  <tr><th>Duración</th><td><?=$duracion?> días (<?=$inicio?> → <?=$fin?>)</td></tr>
  <tr><th>Guía</th>   <td><?=htmlspecialchars($g['nombre_paquete_guia'])?> – $<?=number_format($g['precio'],2)?></td></tr>
  <tr><th>Hab.</th>    <td><?=htmlspecialchars($h['nombre_tipo'])?> – $<?=number_format($h['precio'],2)?></td></tr>
  <tr><th>Autos</th>   <td><?=$cantidad_autos?> unidad(es) × $50/día × <?=$duracion?> días = $<?=number_format($precio_autos,2)?></td></tr>
  <tr><th>Total</th>   <td><strong>$<?=number_format($precio_total,2)?></strong></td></tr>
  <tr><th>Cliente</th> <td><?=htmlspecialchars($nombre_cliente)?> (<?=$email_cliente?>)</td></tr>
</table>

<a href="mis_reservas.php">Ver todas mis reservas</a>  
<a href="index.php" style="margin-left:20px">&larr; Volver a Paquetes</a>

</body>
</html>
