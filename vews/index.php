<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "turismo");
if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

$sql = "SELECT * FROM ViewFlightPackages";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Paquetes de Vuelo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Montserrat', sans-serif; background:#eef2f5; color:#333; min-height:100vh; padding-bottom: 100px; }

    nav {
      background: #fff;
      padding: 15px 40px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      position: sticky; top:0; z-index:100;
      display:flex; justify-content:space-between; align-items:center;
    }
    nav h1 { font-weight: 700; color:#007BFF; font-size:1.4rem; }
    nav .nav-links a, nav .nav-links span {
      margin-left: 20px;
      text-decoration:none;
      font-weight:500;
      color:#555;
      position:relative;
      padding-bottom:3px;
    }
    nav .nav-links a:hover {
      color:#007BFF;
    }
    nav .nav-links a:hover::after {
      content:''; position:absolute; bottom:0; left:0; right:0;
      height:2px; background:#007BFF;
    }

    main { max-width:1200px; margin:30px auto; padding:0 20px; }
    .grid {
      display:grid;
      grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));
      gap:30px;
    }

    .card {
      background:#fff;
      border-radius:12px;
      overflow:hidden;
      box-shadow:0 8px 16px rgba(0,0,0,0.08);
      transition:transform .3s, box-shadow .3s;
      display:flex; flex-direction:column;
    }
    .card:hover {
      transform:translateY(-8px);
      box-shadow:0 16px 32px rgba(0,0,0,0.12);
    }
    .card img {
      width:100%; height:160px; object-fit:cover;
    }
    .card-content {
      flex:1; padding:20px;
    }
    .card-content h2 {
      font-size:1.2rem; margin-bottom:10px; color:#007BFF;
    }
    .card-content p {
      font-size:.95rem; margin-bottom:8px; line-height:1.4;
      color:#555;
    }
    .card-content p em {
      color:#666;
      font-style:italic;
    }
    .card-footer {
      padding:15px 20px; background:#fafafa; text-align:right;
    }
    .card-footer a.btn {
      display:inline-block;
      background:#28a745;
      color:#fff;
      padding:10px 18px;
      border-radius:6px;
      text-decoration:none;
      font-weight:500;
      transition:background .3s;
    }
    .card-footer a.btn:hover {
      background:#218838;
    }

    /* Footer fijo con expansión al fondo */
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
    <h1>Agencia Viajes</h1>
    <div class="nav-links">
      <span>Hola, <?=htmlspecialchars($_SESSION['usuario'])?></span>
      <a href="mis_reservas.php">Mis Reservas</a>
      <a href="logout.php" style="color:#dc3545;">Salir</a>
    </div>
  </nav>

  <main>
    <?php if ($result && $result->num_rows): ?>
      <div class="grid">
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="card">
            <?php if (!empty($row['destino_imagen'])): ?>
              <img src="assets/images/<?= htmlspecialchars($row['destino_imagen']) ?>" alt="<?= htmlspecialchars($row['destino_ciudad']) ?>">
            <?php else: ?>
              <img src="assets/images/default.jpg" alt="Imagen genérica">
            <?php endif; ?>

            <div class="card-content">
              <h2><?=htmlspecialchars($row['nombre_paquete'])?></h2>
              <p><strong>Destino:</strong> <?=htmlspecialchars($row['destino_ciudad'])?>, <?=htmlspecialchars($row['destino_pais'])?></p>
              <p><strong>Duración:</strong> <?=$row['duracion_dias']?> días</p>
              <p><strong>Precio:</strong> $<?=number_format($row['precio_total_estimado'],2)?></p>
              <p><em><?=htmlspecialchars($row['descripcion'])?></em></p>
            </div>

            <div class="card-footer">
              <a class="btn" href="detalle_paquete.php?id=<?=$row['id_paquete']?>">Ver Detalles</a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p style="text-align:center; font-style:italic; margin-top:50px;">No hay paquetes disponibles.</p>
    <?php endif; ?>
  </main>

  <footer class="footer" id="footer">
  &copy; <?=date('Y')?> Agencia de Viajes. Todos los derechos reservados.
    <div class="icons">
      <a href="mailto:tuempresa@email.com" title="Enviar correo">
        <i class="fas fa-envelope"></i>
      </a>
      <a href="https://wa.me/5491234567890" target="_blank" title="WhatsApp">
        <i class="fab fa-whatsapp"></i>
      </a>
      <a href="quienes_somos.php" title="¿Quiénes somos?">
        <i class="fas fa-circle-exclamation"></i>
      </a>
    </div>
  </footer>

  <script>
    const footer = document.getElementById('footer');
    window.addEventListener('scroll', () => {
      if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 5) {
        footer.classList.add('expanded');
      } else {
        footer.classList.remove('expanded');
      }
    });
  </script>

</body>
</html>
<?php $conn->close(); ?>
