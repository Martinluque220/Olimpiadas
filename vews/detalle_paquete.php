<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$db = "turismo";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Validar que viene el id del paquete por GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de paquete no válido.");
}

$id_paquete = intval($_GET['id']);

// Consulta datos básicos del paquete con destino
$sqlPaquete = "SELECT 
    p.*, 
    d.ciudad AS destino_ciudad, 
    d.pais AS destino_pais 
FROM Paquetes p
JOIN Destinos d ON p.id_destino = d.id_destino
WHERE p.id_paquete = ?";
$stmt = $conn->prepare($sqlPaquete);
$stmt->bind_param("i", $id_paquete);
$stmt->execute();
$resultPaquete = $stmt->get_result();

if ($resultPaquete->num_rows == 0) {
    die("Paquete no encontrado.");
}

$paquete = $resultPaquete->fetch_assoc();

// Consultar servicios asociados al paquete
$sqlServicios = "SELECT 
    s.nombre_servicio, 
    s.descripcion_servicio, 
    s.tipo_servicio, 
    s.precio_unitario_base, 
    dps.cantidad, 
    dps.costo_adicional_paquete, 
    dps.notas_especificas_paquete
FROM DetallePaqueteServicio dps
JOIN Servicios s ON dps.id_servicio = s.id_servicio
WHERE dps.id_paquete = ?";
$stmt2 = $conn->prepare($sqlServicios);
$stmt2->bind_param("i", $id_paquete);
$stmt2->execute();
$resultServicios = $stmt2->get_result();

// Calcular el precio total estimado
$precio_total = floatval($paquete['precio_base']);
$servicios_temp = [];

while ($servicio = $resultServicios->fetch_assoc()) {
    $precio_total += floatval($servicio['precio_unitario_base']) * intval($servicio['cantidad']);
    $precio_total += floatval($servicio['costo_adicional_paquete']);
    $servicios_temp[] = $servicio; // Guardamos para reutilizar en el HTML
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Paquete: <?= htmlspecialchars($paquete['nombre_paquete']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #fafafa; padding: 20px; max-width: 800px; margin: auto; }
        h1 { color: #007BFF; }
        .info, .servicios { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .servicios h2 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #007BFF; color: white; }
        a.back-link {
            display: inline-block;
            margin-bottom: 15px;
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }
        a.back-link:hover { text-decoration: underline; }

        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 15px;
        }
        .btn:hover { background-color: #218838; }
    </style>
</head>
<body>

<a class="back-link" href="index.php">&larr; Volver a Paquetes</a>

<h1><?= htmlspecialchars($paquete['nombre_paquete']) ?></h1>

<div class="info">
    <p><strong>Descripción:</strong> <?= htmlspecialchars($paquete['descripcion']) ?></p>
    <p><strong>Destino:</strong> <?= htmlspecialchars($paquete['destino_ciudad']) ?>, <?= htmlspecialchars($paquete['destino_pais']) ?></p>
    <p><strong>Duración:</strong> <?= $paquete['duracion_dias'] ?> días</p>
    <p><strong>Precio Base:</strong> $<?= number_format($paquete['precio_base'], 2) ?></p>
    <p><strong>Disponibilidad:</strong> Desde <?= $paquete['fecha_inicio_disponibilidad'] ?> hasta <?= $paquete['fecha_fin_disponibilidad'] ?></p>
    <p><strong>Precio Total Estimado:</strong> $<?= number_format($precio_total, 2) ?></p>
    <a class="btn" href="reservar.php?id=<?= $id_paquete ?>">Reservar Paquete</a>
</div>

<div class="servicios">
    <h2>Servicios Incluidos</h2>
    <?php if (count($servicios_temp) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Servicio</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Costo Adicional</th>
                <th>Precio Unitario</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicios_temp as $servicio): ?>
            <tr>
                <td><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                <td><?= htmlspecialchars($servicio['descripcion_servicio']) ?></td>
                <td><?= htmlspecialchars($servicio['tipo_servicio']) ?></td>
                <td><?= $servicio['cantidad'] ?></td>
                <td>$<?= number_format($servicio['costo_adicional_paquete'], 2) ?></td>
                <td>$<?= number_format($servicio['precio_unitario_base'], 2) ?></td>
                <td><?= htmlspecialchars($servicio['notas_especificas_paquete']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No hay servicios asociados a este paquete.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$stmt->close();
$stmt2->close();
$conn->close();
?>
