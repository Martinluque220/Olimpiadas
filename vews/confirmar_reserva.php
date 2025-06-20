<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost","root","","turismo");
if ($conn->connect_error) die("Error BD: ".$conn->connect_error);

// 1) Asegurarnos de tener un cliente para este usuario
$email = $_SESSION['email'] ?? '';
$nombreUsuario = $_SESSION['usuario'];

// 1a) Buscar cliente por email
$stmtC = $conn->prepare("SELECT id_cliente FROM Clientes WHERE email = ?");
$stmtC->bind_param("s", $email);
$stmtC->execute();
$resC = $stmtC->get_result();

if ($resC->num_rows > 0) {
    $id_cliente = $resC->fetch_assoc()['id_cliente'];
} else {
    // 1b) Insertar nuevo cliente con nombre de usuario como 'nombre' y apellido vacío
    $stmtI = $conn->prepare("INSERT INTO Clientes (nombre, apellido, email) VALUES (?, '', ?)");
    $stmtI->bind_param("ss", $nombreUsuario, $email);
    $stmtI->execute();
    $id_cliente = $stmtI->insert_id;
    $stmtI->close();
}
$stmtC->close();

// --- resto de tu lógica de reserva ---

// Validar ID de paquete
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Paquete no válido");
}
$id_paquete = intval($_GET['id']);

// Recoger POST
$duracion       = intval($_POST['duracion'] ?? 1);
$id_guia        = intval($_POST['paquete_guia'] ?? 0);
$cantidad_autos = intval($_POST['cantidad_autos'] ?? 0);
$id_habitacion  = intval($_POST['tipo_habitacion'] ?? 0);

// Precio base
$stmt = $conn->prepare("SELECT precio_base FROM Paquetes WHERE id_paquete=?");
$stmt->bind_param("i",$id_paquete);
$stmt->execute();
$precio_base = $stmt->get_result()->fetch_assoc()['precio_base'];
$stmt->close();

// Precio guía
$stmt = $conn->prepare("SELECT precio FROM PaquetesGuia WHERE id_paquete_guia=?");
$stmt->bind_param("i",$id_guia);
$stmt->execute();
$precio_guia = $stmt->get_result()->fetch_assoc()['precio'];
$stmt->close();

// Precio habitación
$stmt = $conn->prepare("SELECT precio FROM TiposHabitacion WHERE id_tipo_habitacion=?");
$stmt->bind_param("i",$id_habitacion);
$stmt->execute();
$precio_hab = $stmt->get_result()->fetch_assoc()['precio'];
$stmt->close();

// Calcular total
$precio_total = $precio_base + $precio_guia + $precio_hab + (50 * $cantidad_autos * $duracion);

// Fechas
$inicio = date('Y-m-d');
$fin    = date('Y-m-d', strtotime("+$duracion days"));

// Datos usuario
$id_usuario     = $_SESSION['id_usuario'];
$nombre_cliente = $nombreUsuario;
$email_cliente  = $email;

// Insertar reserva
$sql = "INSERT INTO Reservas (
    id_cliente, id_paquete, nombre_cliente, email_cliente,
    fecha_viaje_inicio, fecha_viaje_fin, estado_reserva,
    precio_total_reserva, numero_pasajeros, duracion_dias,
    id_paquete_guia, cantidad_autos, id_tipo_habitacion, id_usuario
) VALUES (?, ?, ?, ?, ?, ?, 'Confirmada', ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$num_pasajeros = 1;
$stmt->bind_param(
    "iissssdiisiii",
    $id_cliente,
    $id_paquete,
    $nombre_cliente,
    $email_cliente,
    $inicio,
    $fin,
    $precio_total,
    $num_pasajeros,
    $duracion,
    $id_guia,
    $cantidad_autos,
    $id_habitacion,
    $id_usuario
);

if (!$stmt->execute()) {
    die("Error al crear la reserva: " . $stmt->error);
}

$stmt->close();
$conn->close();

// Redirigir a Mis Reservas
header("Location: mis_reservas.php");
exit;
