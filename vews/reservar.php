<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost","root","","turismo");
if ($conn->connect_error) {
    die("Error BD: ".$conn->connect_error);
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Paquete no válido");
}
$id_paquete = intval($_GET['id']);

// Obtener datos del paquete
$stmt = $conn->prepare("
  SELECT p.nombre_paquete, d.ciudad AS destino, d.pais AS pais
  FROM Paquetes p
  JOIN Destinos d ON p.id_destino = d.id_destino
  WHERE p.id_paquete=?
");
$stmt->bind_param("i", $id_paquete);
$stmt->execute();
$paquete = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener guías y tipos de habitación
$guias = $conn->query("SELECT * FROM PaquetesGuia");
$habs  = $conn->query("SELECT * FROM TiposHabitacion");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reservar “<?=htmlspecialchars($paquete['nombre_paquete'])?>”</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Montserrat',sans-serif; background:#f0f2f5; color:#333; min-height:100vh; padding-bottom:100px; }

    header {
      background:#fff; padding:15px 30px;
      box-shadow:0 2px 8px rgba(0,0,0,0.1);
      display:flex; align-items:center; justify-content:space-between;
      position:sticky; top:0; z-index:100;
    }
    header h1 { font-size:1.4rem; color:#007BFF; font-weight:700; }
    header nav a {
      margin-left:20px; text-decoration:none; color:#555; font-weight:500;
      transition:color .2s;
    }
    header nav a:hover { color:#007BFF; }

    .breadcrumbs {
      max-width:600px; margin:20px auto; font-size:.9rem; color:#555;
    }
    .breadcrumbs a { color:#007BFF; text-decoration:none; }
    .breadcrumbs a:hover { text-decoration:underline; }

    .container {
      max-width:600px; margin:0 auto 40px;
      background:#fff; border-radius:8px;
      padding:30px; box-shadow:0 4px 16px rgba(0,0,0,0.08);
    }
    .container h2 {
      margin-bottom:20px; text-align:center;
      color:#007BFF; font-size:1.6rem; font-weight:500;
    }
    form {
      display:grid;
      grid-template-columns:1fr;
      gap:20px;
    }
    @media(min-width:480px) {
      form {grid-template-columns:1fr 1fr;}
      form .full {grid-column:1/-1;}
    }
    label {
      display:block; margin-bottom:6px; font-weight:500;
    }
    input[type="number"],
    select {
      width:100%; padding:10px; border:1px solid #ccc;
      border-radius:4px; font-size:1rem;
      transition:border-color .2s;
    }
    input:focus, select:focus {
      border-color:#007BFF; outline:none;
    }
    .radio-group {
      display:flex; flex-wrap:wrap; gap:10px;
    }
    .radio-group label {
      display:flex; align-items:center; padding:8px 12px;
      border:1px solid #ccc; border-radius:4px;
      cursor:pointer; transition:background .2s, border-color .2s;
      font-size:.95rem;
    }
    .radio-group input {
      margin-right:8px;
    }
    .radio-group input:checked + span {
      color:#007BFF; font-weight:700;
    }
    .btn-submit {
      grid-column:1/-1; text-align:center;
    }
    .btn-submit button {
      background:#28a745; color:#fff; border:none;
      padding:12px 30px; font-size:1rem; font-weight:500;
      border-radius:50px; cursor:pointer;
      transition:background .2s, transform .2s;
    }
    .btn-submit button:hover {
      background:#218838; transform:translateY(-2px);
    }

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

<header>
  <h1>Agencia de Viajes</h1>
  <nav>
    <a href="index.php">Paquetes</a>
    <a href="mis_reservas.php">Mis Reservas</a>
    <a href="logout.php" style="color:#dc3545;">Salir</a>
  </nav>
</header>

<div class="breadcrumbs">
  <a href="index.php">Inicio</a> &raquo;
  <a href="detalle_paquete.php?id=<?=$id_paquete?>">
    <?=htmlspecialchars($paquete['nombre_paquete'])?>
  </a> &raquo;
  <span>Reservar</span>
</div>

<div class="container">
  <h2>Reservar: <?=htmlspecialchars($paquete['nombre_paquete'])?></h2>

  <form method="post" action="confirmar_reserva.php?id=<?=$id_paquete?>">
    <div>
      <label for="duracion">Duración (días)</label>
      <input type="number" id="duracion" name="duracion"
             value="1" min="1" required>
    </div>

    <div>
      <label for="cantidad_autos">Autos por día</label>
      <input type="number" id="cantidad_autos" name="cantidad_autos"
             value="0" min="0">
    </div>

    <div class="full">
      <label>Paquete Guía Turístico</label>
      <div class="radio-group">
        <?php while($g=$guias->fetch_assoc()): ?>
          <label>
            <input type="radio" name="paquete_guia"
                   value="<?=$g['id_paquete_guia']?>" required>
            <span>
              <?=htmlspecialchars($g['nombre_paquete_guia'])?>
              – $<?=number_format($g['precio'],2)?>
            </span>
          </label>
        <?php endwhile; ?>
      </div>
    </div>

    <div class="full">
      <label for="tipo_habitacion">Tipo de habitación</label>
      <select id="tipo_habitacion" name="tipo_habitacion" required>
        <?php while($h=$habs->fetch_assoc()): ?>
          <option value="<?=$h['id_tipo_habitacion']?>">
            <?=htmlspecialchars($h['nombre_tipo'])?>
            – $<?=number_format($h['precio'],2)?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="btn-submit">
      <button type="submit">Confirmar Reserva</button>
    </div>
  </form>
</div>

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
<?php $conn->close(); ?>
