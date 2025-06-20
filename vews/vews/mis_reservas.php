<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "turismo");
if ($conn->connect_error) die("Error BD: " . $conn->connect_error);

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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Montserrat', sans-serif; background:#eef2f5; color:#333; min-height:100vh; padding-bottom:100px; }

    nav {
      background:#fff; padding:15px 40px;
      box-shadow:0 2px 8px rgba(0,0,0,0.1);
      display:flex; justify-content:space-between; align-items:center;
      position:sticky; top:0; z-index:100;
    }
    nav h1 { color:#007BFF; font-weight:700; font-size:1.4rem; }
    nav .nav-links a, nav .nav-links span {
      margin-left:20px; text-decoration:none; color:#555; font-weight:500;
    }
    nav .nav-links a:hover { color:#007BFF; }

    main { max-width:1000px; margin:40px auto; padding:0 20px; }
    .grid {
      display:grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap:30px;
    }
    .card {
      background:#fff; border-radius:12px; overflow:hidden;
      box-shadow:0 8px 16px rgba(0,0,0,0.08);
      transition:transform .2s, box-shadow .2s;
      display:flex; flex-direction:column;
    }
    .card:hover {
      transform:translateY(-5px); box-shadow:0 12px 24px rgba(0,0,0,0.12);
    }
    .card-header {
      background:#007BFF; color:#fff; padding:15px;
      display:flex; justify-content:space-between; align-items:center;
    }
    .card-header h2 { font-size:1rem; font-weight:500; }
    .badge {
      padding:4px 8px; border-radius:4px; font-size:.8rem; font-weight:500;
      color:#fff;
    }
    .badge.Pendiente { background:#ffc107; }
    .badge.Confirmada { background:#28a745; }
    .badge.Cancelada { background:#dc3545; }

    .card-body { padding:20px; flex:1; }
    .card-body p { margin-bottom:10px; font-size:.95rem; }
    .card-body p span { font-weight:500; }

    .card-footer {
      padding:15px 20px; background:#fafafa; text-align:right;
    }
    .btn-cancel {
      background:#dc3545; color:#fff; padding:8px 14px;
      border:none; border-radius:5px; text-decoration:none; font-size:.9rem;
      transition:background .2s;
    }
    .btn-cancel:hover { background:#c82333; }

    .empty {
      text-align:center; margin-top:60px; font-style:italic; color:#555;
    }

    /* Footer moderno expandible */
    .footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      background: linear-gradient(90deg, #007BFF, #00B4D8);
      color: white;
      padding: 10px 0;
      text-align: center;
      transition: height 0.3s ease;
      z-index: 999;
    }
    .footer.expanded {
      height: 80px;
      padding-top: 20px;
    }
    .footer .icons {
      display: flex;
      justify-content: center;
      gap: 30px;
      font-size: 1.8rem;
    }
    .footer .icons a {
      color: white;
      text-decoration: none;
      transition: transform 0.2s ease;
    }
    .footer .icons a:hover {
      transform: scale(1.2);
      color: #ffeb3b;
    }
  </style>
</head>
<body>

<nav>
  <h1>Mis Reservas</h1>
  <div class="nav-links">
    <span>Hola, <?=htmlspecialchars($_SESSION['usuario'])?></span>
    <a href="index.php">Paquetes</a>
    <a href="logout.php" style="color:#dc3545;">Salir</a>
  </div>
</nav>

<main>
  <?php if ($result->num_rows): ?>
    <div class="grid">
      <?php while($r = $result->fetch_assoc()): ?>
      <div class="card">
        <div class="card-header">
          <h2>Reserva #<?=$r['id_reserva']?></h2>
          <span class="badge <?=htmlspecialchars($r['estado_reserva'])?>">
            <?=htmlspecialchars($r['estado_reserva'])?>
          </span>
        </div>
        <div class="card-body">
          <p><span>Paquete:</span> <?=htmlspecialchars($r['nombre_paquete'])?></p>
          <p><span>Duración:</span> <?=$r['duracion_dias']?> días</p>
          <p><span>Fechas:</span> <?=$r['fecha_viaje_inicio']?> → <?=$r['fecha_viaje_fin']?></p>
          <p><span>Guía:</span> <?=htmlspecialchars($r['guia'] ?? '—')?></p>
          <p><span>Habitación:</span> <?=htmlspecialchars($r['habitacion'] ?? '—')?></p>
          <p><span>Autos:</span> <?=$r['cantidad_autos']?></p>
          <p><span>Total:</span> $<?=number_format($r['precio_total_reserva'],2)?></p>
        </div>
        <div class="card-footer">
          <a class="btn-cancel" href="cancelar_reserva.php?id=<?=$r['id_reserva']?>">Cancelar</a>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p class="empty">
      Aún no tienes reservas.<br>
      <a href="index.php" style="color:#007BFF; text-decoration:underline;">Haz tu primera reserva</a>
    </p>
  <?php endif; ?>
</main>

<footer class="footer" id="footer">
&copy; <?=date('Y')?> Agencia de Viajes. Todos los derechos reservados.
  <div class="icons">
    <a href="mailto:viajes@agencia.com" title="Correo">
      <i class="fas fa-envelope"></i>
    </a>
    <a href="https://wa.me/5491234567890" target="_blank" title="WhatsApp">
      <i class="fab fa-whatsapp"></i>
    </a>
    <a href="quienes_somos.php" title="Quiénes somos">
      <i class="fas fa-circle-exclamation"></i>
    </a>
  </div>
</footer>

<script>
  const footer = document.getElementById('footer');
  window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 10) {
      footer.classList.add('expanded');
    } else {
      footer.classList.remove('expanded');
    }
  });
</script>

</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
