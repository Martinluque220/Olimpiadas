<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
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

// Consulta a la vista de paquetes con vuelo
$sql = "SELECT * FROM ViewFlightPackages";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Paquetes de Vuelo</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 20px; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h1 { color: #333; margin: 0; }
        .btn {
            background-color: #007BFF;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn:hover { background-color: #0056b3; }

        .container {
            display: flex;
            flex-direction: column; /* apilar verticalmente */
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            width: 300px;
        }

        .card h2 {
            color: #007BFF;
            font-size: 20px;
            margin-top: 0;
        }

        .card p { margin: 6px 0; }
    </style>
</head>
<body>

<div class="header">
    <h1>Paquetes de Vuelo Disponibles</h1>
    <a class="btn" href="mis_reservas.php">Mis Reservas</a>
</div>

<div class="container">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card">
                <h2><?= htmlspecialchars($row['nombre_paquete']) ?></h2>
                <p><strong>Destino:</strong> <?= htmlspecialchars($row['destino_ciudad']) ?>, <?= htmlspecialchars($row['destino_pais']) ?></p>
                <p><strong>Duración:</strong> <?= $row['duracion_dias'] ?> días</p>
                <p><strong>Precio Estimado:</strong> $<?= number_format($row['precio_total_estimado'], 2) ?></p>
                <p><strong>Desde:</strong> <?= $row['fecha_inicio_disponibilidad'] ?></p>
                <p><strong>Hasta:</strong> <?= $row['fecha_fin_disponibilidad'] ?></p>
                <p><em><?= htmlspecialchars($row['descripcion']) ?></em></p>

                <a class="btn" href="detalle_paquete.php?id=<?= $row['id_paquete'] ?>">Ver Detalles</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No hay paquetes de vuelo disponibles.</p>
    <?php endif; ?>
</div>

<?php $conn->close(); ?>
</body>
</html>
