<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "turismo");
if ($conn->connect_error) die("Error BD: " . $conn->connect_error);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de paquete no válido.");
}
$id_paquete = intval($_GET['id']);

// Datos del paquete
$stmt = $conn->prepare("
  SELECT p.*, d.ciudad AS destino_ciudad, d.pais AS destino_pais, d.imagen AS destino_imagen
  FROM Paquetes p
  JOIN Destinos d ON p.id_destino = d.id_destino
  WHERE p.id_paquete = ?
");
$stmt->bind_param("i", $id_paquete);
$stmt->execute();
$paquete = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Servicios
$stmt2 = $conn->prepare("
  SELECT s.nombre_servicio, s.descripcion_servicio, s.tipo_servicio,
         s.precio_unitario_base, dps.cantidad, dps.costo_adicional_paquete, dps.notas_especificas_paquete
  FROM DetallePaqueteServicio dps
  JOIN Servicios s ON dps.id_servicio=s.id_servicio
  WHERE dps.id_paquete=?
");
$stmt2->bind_param("i", $id_paquete);
$stmt2->execute();
$servicios = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt2->close();

// Cálculo total
$total = $paquete['precio_base'];
foreach($servicios as $srv){
    $total += $srv['precio_unitario_base'] * $srv['cantidad'] + $srv['costo_adicional_paquete'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalles: <?=htmlspecialchars($paquete['nombre_paquete'])?></title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Montserrat',sans-serif; background:#f0f2f5; color:#333; min-height:100vh; padding-bottom:100px; }

    header { background:#fff; padding:10px 20px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    header nav a { text-decoration:none; color:#007BFF; font-weight:500; margin-right:10px; }

    main { max-width:1000px; margin:20px auto; padding:0 20px; position:relative; }
    .detail-card {
      display:flex; flex-wrap:wrap; background:#fff;
      border-radius:8px; overflow:hidden;
      box-shadow:0 4px 12px rgba(0,0,0,0.05);
    }
    .detail-image {
      flex:1 1 300px; min-height:250px;
      background:#ddd url('assets/images/<?=htmlspecialchars($paquete['destino_imagen'])?>') center/cover no-repeat;
    }
    .detail-info {
      flex:2 1 400px; padding:20px;
    }
    .detail-info h1 { color:#007BFF; margin-bottom:10px; font-size:1.8rem; }
    .detail-info p { margin:8px 0; font-size:1rem; }
    .detail-info p span { font-weight:500; }

    .services {
      margin-top:20px;
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
      gap:15px;
    }
    .service {
      background:#fff; border-radius:6px;
      padding:12px;
      box-shadow:0 2px 8px rgba(0,0,0,0.05);
    }
    .service h3 { font-size:1rem; color:#007BFF; margin-bottom:6px; }
    .service p { font-size:.9rem; margin-bottom:4px; }

    .total-box {
      margin-top:20px;
      font-size:1.2rem;
      font-weight:500;
      text-align:right;
    }

    .btn-reserve {
      position:fixed; bottom:350px; right:480px;
      background:#28a745; color:#fff;
      padding:14px 24px;
      border:none;
      border-radius:50px;
      font-size:1rem;
      font-weight:500;
      box-shadow:0 4px 12px rgba(0,0,0,0.2);
      text-decoration:none;
      transition:background .2s, transform .2s;
      z-index: 200;
    }
    .btn-reserve:hover {
      background:#218838;
      transform:translateY(-2px);
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

    @media(max-width:600px){
      .detail-info h1 { font-size:1.4rem; }
      .btn-reserve { bottom:20px; right:20px; padding:12px 20px; }
    }
  </style>
</head>
<body>

<header>
  <nav>
    <a href="index.php">&larr; Paquetes</a> /
    <span>Detalles</span>
  </nav>
</header>

<main>
  <div class="detail-card">
    <div class="detail-image"></div>
    <div class="detail-info">
      <h1><?=htmlspecialchars($paquete['nombre_paquete'])?></h1>
      <p><span>Destino:</span> <?=htmlspecialchars($paquete['destino_ciudad'])?>, <?=htmlspecialchars($paquete['destino_pais'])?></p>
      <p><span>Duración:</span> <?=$paquete['duracion_dias']?> días</p>
      <p><span>Disponibilidad:</span> <?=$paquete['fecha_inicio_disponibilidad']?> → <?=$paquete['fecha_fin_disponibilidad']?></p>
      <p><span>Precio Base:</span> $<?=number_format($paquete['precio_base'],2)?></p>
      <p><span>Descripción:</span> <?=htmlspecialchars($paquete['descripcion'])?></p>
    </div>
  </div>

  <section class="services">
    <?php if(count($servicios)): foreach($servicios as $s): ?>
      <div class="service">
        <h3><?=htmlspecialchars($s['nombre_servicio'])?></h3>
        <p><strong>Tipo:</strong> <?=htmlspecialchars($s['tipo_servicio'])?></p>
        <p><strong>Cantidad:</strong> <?=$s['cantidad']?></p>
        <p><strong>Unidad:</strong> $<?=number_format($s['precio_unitario_base'],2)?></p>
        <p><strong>Costo Adic.:</strong> $<?=number_format($s['costo_adicional_paquete'],2)?></p>
        <p><strong>Notas:</strong> <?=htmlspecialchars($s['notas_especificas_paquete'])?></p>
      </div>
    <?php endforeach; else: ?>
      <p style="grid-column:1/-1;text-align:center;color:#555">No hay servicios para este paquete.</p>
    <?php endif; ?>
  </section>

  <div class="total-box">
    Total Estimado: $<?=number_format($total,2)?>
  </div>
  <a class="btn-reserve" href="reservar.php?id=<?=$id_paquete?>">Reservar Ahora</a>
</main>

<footer class="footer" id="footer">
&copy; <?=date('Y')?> Agencia de Viajes. Todos los derechos reservados.
  <div class="icons">
    <a href="mailto:tuempresa@email.com" title="Correo">
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
<?php $conn->close(); ?>
