-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-06-2025 a las 00:54:30
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `turismo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre`, `apellido`, `email`, `telefono`, `direccion`) VALUES
(1, 'Juan', 'Pérez', 'juan@example.com', '123456789', 'Calle Falsa 123'),
(2, 'mono', '', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinos`
--

CREATE TABLE `destinos` (
  `id_destino` int(11) NOT NULL,
  `nombre_destino` varchar(100) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `destinos`
--

INSERT INTO `destinos` (`id_destino`, `nombre_destino`, `ciudad`, `pais`, `imagen`) VALUES
(1, 'Miami Beach Getaway', 'Miami', 'Estados Unidos', 'destino_1.png'),
(2, 'Escapada a Madrid', 'Madrid', 'España', 'destino_2.png'),
(3, 'Aventura en Bariloche', 'San Carlos de Bariloche', 'Argentina', 'destino_3.png'),
(4, 'Aventura en Machu Picchu', 'Cusco', 'Perú', 'machu_picchu.jpg'),
(5, 'Turismo urbano en Tokio', 'Tokio', 'Japón', 'tokio.jpg'),
(6, 'Relax en Punta Cana', 'Punta Cana', 'República Dominicana', 'punta_cana.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallepaqueteservicio`
--

CREATE TABLE `detallepaqueteservicio` (
  `id_detalle` int(11) NOT NULL,
  `id_paquete` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `notas_especificas_paquete` text DEFAULT NULL,
  `costo_adicional_paquete` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detallepaqueteservicio`
--

INSERT INTO `detallepaqueteservicio` (`id_detalle`, `id_paquete`, `id_servicio`, `cantidad`, `notas_especificas_paquete`, `costo_adicional_paquete`) VALUES
(1, 1, 1, 1, 'Vuelo directo clase económica', 0.00),
(2, 2, 2, 1, 'Vuelo con escala corta', 0.00),
(3, 3, 3, 1, 'Vuelo de cabotaje', 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetes`
--

CREATE TABLE `paquetes` (
  `id_paquete` int(11) NOT NULL,
  `nombre_paquete` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_base` decimal(10,2) DEFAULT NULL,
  `duracion_dias` int(11) DEFAULT NULL,
  `fecha_inicio_disponibilidad` date DEFAULT NULL,
  `fecha_fin_disponibilidad` date DEFAULT NULL,
  `id_destino` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquetes`
--

INSERT INTO `paquetes` (`id_paquete`, `nombre_paquete`, `descripcion`, `precio_base`, `duracion_dias`, `fecha_inicio_disponibilidad`, `fecha_fin_disponibilidad`, `id_destino`) VALUES
(1, 'Playas y Sol en Miami', 'Vacaciones de 5 días en Miami con hotel y vuelo incluidos', 500.00, 5, '2025-07-01', '2025-12-31', 1),
(2, 'Cultura y Tapas en Madrid', 'Tour de 4 días con hotel + vuelo + city tour', 400.00, 4, '2025-08-15', '2026-01-31', 2),
(3, 'Naturaleza en Bariloche', '3 noches en cabaña + vuelo + trekking', 350.00, 3, '2025-06-15', '2025-11-30', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetesguia`
--

CREATE TABLE `paquetesguia` (
  `id_paquete_guia` int(11) NOT NULL,
  `nombre_paquete_guia` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquetesguia`
--

INSERT INTO `paquetesguia` (`id_paquete_guia`, `nombre_paquete_guia`, `descripcion`, `precio`) VALUES
(1, 'Básico', 'Guía turístico básico con información general', 50.00),
(2, 'Regular', 'Guía con tours adicionales y atención personalizada', 100.00),
(3, 'Premium', 'Guía exclusivo con acceso VIP y servicios especiales', 200.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo_proveedor` varchar(50) DEFAULT NULL,
  `contacto` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id_proveedor`, `nombre`, `tipo_proveedor`, `contacto`) VALUES
(1, 'Aerolineas Argentinas', 'Aerolínea', 'contacto@aerolineas.com'),
(2, 'LATAM Airlines', 'Aerolínea', 'info@latam.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_paquete` int(11) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `email_cliente` varchar(100) DEFAULT NULL,
  `fecha_reserva` date DEFAULT curdate(),
  `fecha_viaje_inicio` date DEFAULT NULL,
  `fecha_viaje_fin` date DEFAULT NULL,
  `estado_reserva` varchar(50) DEFAULT NULL,
  `precio_total_reserva` decimal(10,2) DEFAULT NULL,
  `numero_pasajeros` int(11) DEFAULT NULL,
  `duracion_dias` int(11) DEFAULT NULL,
  `id_paquete_guia` int(11) DEFAULT NULL,
  `cantidad_autos` int(11) DEFAULT 0,
  `id_tipo_habitacion` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id_reserva`, `id_cliente`, `id_paquete`, `nombre_cliente`, `email_cliente`, `fecha_reserva`, `fecha_viaje_inicio`, `fecha_viaje_fin`, `estado_reserva`, `precio_total_reserva`, `numero_pasajeros`, `duracion_dias`, `id_paquete_guia`, `cantidad_autos`, `id_tipo_habitacion`, `id_usuario`) VALUES
(4, 2, 1, 'mono', '', '2025-06-19', '2025-06-19', '2025-06-24', 'Confirmada', 1000.00, 1, 5, 2, 1, 3, 2),
(9, 2, 1, 'ElMiras', '', '2025-06-19', '2025-06-20', '2025-06-21', 'Confirmada', 800.00, 1, 1, 3, 1, 2, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL,
  `descripcion_servicio` text DEFAULT NULL,
  `tipo_servicio` varchar(50) DEFAULT NULL,
  `precio_unitario_base` decimal(10,2) DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre_servicio`, `descripcion_servicio`, `tipo_servicio`, `precio_unitario_base`, `id_proveedor`) VALUES
(1, 'Vuelo Buenos Aires - Miami', 'Ida y vuelta desde Ezeiza a Miami', 'Transporte', 1200.00, 1),
(2, 'Vuelo Buenos Aires - Madrid', 'Ida y vuelta desde Ezeiza a Barajas', 'Transporte', 950.00, 1),
(3, 'Vuelo Buenos Aires - Bariloche', 'Ida y vuelta a Bariloche', 'Transporte', 300.00, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposhabitacion`
--

CREATE TABLE `tiposhabitacion` (
  `id_tipo_habitacion` int(11) NOT NULL,
  `nombre_tipo` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposhabitacion`
--

INSERT INTO `tiposhabitacion` (`id_tipo_habitacion`, `nombre_tipo`, `precio`) VALUES
(1, 'Simple', 0.00),
(2, 'Doble', 50.00),
(3, 'Suite', 150.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario`, `email`, `password`, `fecha_creacion`) VALUES
(1, 'juanperez', 'juan@example.com', '$2y$10$abcdefghijklmnopqrstuv', '2025-06-19 19:56:26'),
(2, 'mono', 'monazo@gmail.com', '$2y$10$E/uq.VMXXlu/rt.MBNQTWuLfjf39tcBkYBqf8/16X7Ap3PPxtwYU6', '2025-06-19 20:00:06'),
(3, 'ElMiras', 'kukaaa@gamil.com', '$2y$10$cU4CRmD7oUh7a31hhTtX.uN6.0GZil82TBOL4UXgJGUm.43xO7mDK', '2025-06-19 21:48:52');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `viewflightpackages`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `viewflightpackages` (
`id_paquete` int(11)
,`nombre_paquete` varchar(100)
,`descripcion` text
,`duracion_dias` int(11)
,`fecha_inicio_disponibilidad` date
,`fecha_fin_disponibilidad` date
,`destino_ciudad` varchar(100)
,`destino_pais` varchar(100)
,`destino_imagen` varchar(255)
,`precio_total_estimado` decimal(43,2)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `viewflightpackages`
--
DROP TABLE IF EXISTS `viewflightpackages`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `viewflightpackages`  AS SELECT `p`.`id_paquete` AS `id_paquete`, `p`.`nombre_paquete` AS `nombre_paquete`, `p`.`descripcion` AS `descripcion`, `p`.`duracion_dias` AS `duracion_dias`, `p`.`fecha_inicio_disponibilidad` AS `fecha_inicio_disponibilidad`, `p`.`fecha_fin_disponibilidad` AS `fecha_fin_disponibilidad`, `d`.`ciudad` AS `destino_ciudad`, `d`.`pais` AS `destino_pais`, `d`.`imagen` AS `destino_imagen`, `p`.`precio_base`+ coalesce(`suma`.`costo_adicional_total`,0) + coalesce(`suma`.`precio_servicios_total`,0) AS `precio_total_estimado` FROM ((`paquetes` `p` join `destinos` `d` on(`p`.`id_destino` = `d`.`id_destino`)) left join (select `dps`.`id_paquete` AS `id_paquete`,sum(`dps`.`costo_adicional_paquete`) AS `costo_adicional_total`,sum(`s`.`precio_unitario_base` * `dps`.`cantidad`) AS `precio_servicios_total` from (`detallepaqueteservicio` `dps` join `servicios` `s` on(`dps`.`id_servicio` = `s`.`id_servicio`)) where `s`.`tipo_servicio` = 'Transporte' and lcase(`s`.`nombre_servicio`) like 'vuelo%' group by `dps`.`id_paquete`) `suma` on(`p`.`id_paquete` = `suma`.`id_paquete`)) ORDER BY `p`.`id_paquete` ASC ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `destinos`
--
ALTER TABLE `destinos`
  ADD PRIMARY KEY (`id_destino`);

--
-- Indices de la tabla `detallepaqueteservicio`
--
ALTER TABLE `detallepaqueteservicio`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_paquete` (`id_paquete`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `paquetes`
--
ALTER TABLE `paquetes`
  ADD PRIMARY KEY (`id_paquete`),
  ADD KEY `id_destino` (`id_destino`);

--
-- Indices de la tabla `paquetesguia`
--
ALTER TABLE `paquetesguia`
  ADD PRIMARY KEY (`id_paquete_guia`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_paquete` (`id_paquete`),
  ADD KEY `id_paquete_guia` (`id_paquete_guia`),
  ADD KEY `id_tipo_habitacion` (`id_tipo_habitacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`),
  ADD KEY `id_proveedor` (`id_proveedor`);

--
-- Indices de la tabla `tiposhabitacion`
--
ALTER TABLE `tiposhabitacion`
  ADD PRIMARY KEY (`id_tipo_habitacion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `destinos`
--
ALTER TABLE `destinos`
  MODIFY `id_destino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detallepaqueteservicio`
--
ALTER TABLE `detallepaqueteservicio`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `paquetes`
--
ALTER TABLE `paquetes`
  MODIFY `id_paquete` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `paquetesguia`
--
ALTER TABLE `paquetesguia`
  MODIFY `id_paquete_guia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tiposhabitacion`
--
ALTER TABLE `tiposhabitacion`
  MODIFY `id_tipo_habitacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detallepaqueteservicio`
--
ALTER TABLE `detallepaqueteservicio`
  ADD CONSTRAINT `detallepaqueteservicio_ibfk_1` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes` (`id_paquete`) ON DELETE CASCADE,
  ADD CONSTRAINT `detallepaqueteservicio_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`);

--
-- Filtros para la tabla `paquetes`
--
ALTER TABLE `paquetes`
  ADD CONSTRAINT `paquetes_ibfk_1` FOREIGN KEY (`id_destino`) REFERENCES `destinos` (`id_destino`);

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes` (`id_paquete`),
  ADD CONSTRAINT `reservas_ibfk_3` FOREIGN KEY (`id_paquete_guia`) REFERENCES `paquetesguia` (`id_paquete_guia`),
  ADD CONSTRAINT `reservas_ibfk_4` FOREIGN KEY (`id_tipo_habitacion`) REFERENCES `tiposhabitacion` (`id_tipo_habitacion`),
  ADD CONSTRAINT `reservas_ibfk_5` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD CONSTRAINT `servicios_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
