<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$host = "localhost";
$user = "root";
$password = "";
$db = "turismo";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener todas las reservas del usuario
$sql = "
  SELECT
    r.id_reserva,
    p.nombre_paquete,
    r.duracion_dias,
    r.fecha_viaje_inicio,
    r.fecha_viaje_fin,
    g.nombre_paquete_guia AS guia,
    h.nombre_tipo           AS habitacion,
    r.cantidad_autos,
    r.precio_total_reserva,
    r.estado_reserva
  FROM Reservas r
  JOIN Paquetes p ON r.id_paquete = p.id_paquete
  LEFT JOIN PaquetesGuia g ON r.id_paquete_guia = g.id_paquete_guia
  LEFT JOIN TiposHabitacion h ON r.id_tipo_habitacion = h.id_tipo_habitacion
  WHERE r.id_usuario = ?
  ORDER BY r.id_reserva DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Reservas</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 0 20px; }
    h1 { color: #007BFF; }
    table.cart { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table.cart th, table.cart td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: left;
    }
    table.cart th {
      background-color: #f8f8f8;
    }
    table.cart tr:nth-child(even) {
      background-color: #fafafa;
    }
    .total {
      text-align: right;
      font-size: 1.2em;
      font-weight: bold;
      padding-top: 10px;
    }
    a { color: #007BFF; text-decoration: none; }
    a:hover { text-decoration: underline; }
    .empty { margin-top: 30px; font-style: italic; }
  </style>
</head>
<body>

  <h1>Mis Reservas</h1>
  <a href="index.php">&larr; Volver a Paquetes</a>

  <?php if ($result->num_rows > 0): ?>
    <table class="cart">
      <thead>
        <tr>
          <th>ID Reserva</th>
          <th>Paquete</th>
          <th>Duración</th>
          <th>Fechas</th>
          <th>Guía</th>
          <th>Habitación</th>
          <th>Autos</th>
          <th>Total</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id_reserva'] ?></td>
            <td><?= htmlspecialchars($row['nombre_paquete']) ?></td>
            <td><?= $row['duracion_dias'] ?> días</td>
            <td><?= $row['fecha_viaje_inicio'] ?> &rarr; <?= $row['fecha_viaje_fin'] ?></td>
            <td><?= htmlspecialchars($row['guia'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['habitacion'] ?? '—') ?></td>
            <td><?= $row['cantidad_autos'] ?></td>
            <td>$<?= number_format($row['precio_total_reserva'], 2) ?></td>
            <td><?= htmlspecialchars($row['estado_reserva']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="empty">No tienes reservas aun. <a href="index.php">Haz tu primera reserva</a>.</p>
  <?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>

</body>
</html>
