-- =============================================
-- ESQUEMA COMPLETO CON DATOS DE EJEMPLO
-- =============================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1) Tablas independientes

CREATE TABLE Destinos (
  id_destino INT AUTO_INCREMENT PRIMARY KEY,
  nombre_destino VARCHAR(100),
  ciudad VARCHAR(100),
  pais VARCHAR(100)
) ENGINE=InnoDB;

CREATE TABLE Proveedores (
  id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  tipo_proveedor VARCHAR(50),
  contacto TEXT
) ENGINE=InnoDB;

CREATE TABLE Usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE Clientes (
  id_cliente INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE,
  telefono VARCHAR(20),
  direccion TEXT
) ENGINE=InnoDB;

-- 2) Tablas que referencian a las anteriores

CREATE TABLE Paquetes (
  id_paquete INT AUTO_INCREMENT PRIMARY KEY,
  nombre_paquete VARCHAR(100) NOT NULL,
  descripcion TEXT,
  precio_base DECIMAL(10,2),
  duracion_dias INT,
  fecha_inicio_disponibilidad DATE,
  fecha_fin_disponibilidad DATE,
  id_destino INT,
  FOREIGN KEY (id_destino) REFERENCES Destinos(id_destino)
) ENGINE=InnoDB;

CREATE TABLE Servicios (
  id_servicio INT AUTO_INCREMENT PRIMARY KEY,
  nombre_servicio VARCHAR(100) NOT NULL,
  descripcion_servicio TEXT,
  tipo_servicio VARCHAR(50),
  precio_unitario_base DECIMAL(10,2),
  id_proveedor INT,
  FOREIGN KEY (id_proveedor) REFERENCES Proveedores(id_proveedor)
) ENGINE=InnoDB;

CREATE TABLE DetallePaqueteServicio (
  id_detalle INT AUTO_INCREMENT PRIMARY KEY,
  id_paquete INT,
  id_servicio INT,
  cantidad INT,
  notas_especificas_paquete TEXT,
  costo_adicional_paquete DECIMAL(10,2) DEFAULT 0,
  FOREIGN KEY (id_paquete) REFERENCES Paquetes(id_paquete) ON DELETE CASCADE,
  FOREIGN KEY (id_servicio) REFERENCES Servicios(id_servicio)
) ENGINE=InnoDB;

-- 3) Tablas de opciones adicionales

CREATE TABLE PaquetesGuia (
  id_paquete_guia INT AUTO_INCREMENT PRIMARY KEY,
  nombre_paquete_guia VARCHAR(50) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE TiposHabitacion (
  id_tipo_habitacion INT AUTO_INCREMENT PRIMARY KEY,
  nombre_tipo VARCHAR(50) NOT NULL,
  precio DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

-- 4) Tabla Reservas

CREATE TABLE Reservas (
  id_reserva INT AUTO_INCREMENT PRIMARY KEY,
  id_cliente INT,
  id_paquete INT NOT NULL,
  nombre_cliente VARCHAR(100) NOT NULL,
  email_cliente VARCHAR(100),
  fecha_reserva DATE DEFAULT CURRENT_DATE,
  fecha_viaje_inicio DATE,
  fecha_viaje_fin DATE,
  estado_reserva VARCHAR(50),
  precio_total_reserva DECIMAL(10,2),
  numero_pasajeros INT,
  duracion_dias INT,
  id_paquete_guia INT,
  cantidad_autos INT DEFAULT 0,
  id_tipo_habitacion INT,
  id_usuario INT,
  FOREIGN KEY (id_cliente)        REFERENCES Clientes(id_cliente),
  FOREIGN KEY (id_paquete)        REFERENCES Paquetes(id_paquete),
  FOREIGN KEY (id_paquete_guia)   REFERENCES PaquetesGuia(id_paquete_guia),
  FOREIGN KEY (id_tipo_habitacion)REFERENCES TiposHabitacion(id_tipo_habitacion),
  FOREIGN KEY (id_usuario)        REFERENCES Usuarios(id_usuario)
) ENGINE=InnoDB;

-- 5) Vista de paquetes con precio estimado

DROP VIEW IF EXISTS ViewFlightPackages;
CREATE VIEW ViewFlightPackages AS
SELECT
    p.id_paquete,
    p.nombre_paquete,
    p.descripcion,
    p.duracion_dias,
    p.fecha_inicio_disponibilidad,
    p.fecha_fin_disponibilidad,
    d.ciudad    AS destino_ciudad,
    d.pais      AS destino_pais,
    p.precio_base
      + COALESCE(suma.costo_adicional_total,0)
      + COALESCE(suma.precio_servicios_total,0)
      AS precio_total_estimado
FROM Paquetes p
JOIN Destinos d ON p.id_destino = d.id_destino
LEFT JOIN (
    SELECT
        dps.id_paquete,
        SUM(dps.costo_adicional_paquete)      AS costo_adicional_total,
        SUM(s.precio_unitario_base * dps.cantidad) AS precio_servicios_total
    FROM DetallePaqueteServicio dps
    JOIN Servicios s ON dps.id_servicio = s.id_servicio
    WHERE s.tipo_servicio = 'Transporte'
      AND LOWER(s.nombre_servicio) LIKE 'vuelo%'
    GROUP BY dps.id_paquete
) suma ON p.id_paquete = suma.id_paquete
ORDER BY p.id_paquete;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- DATOS DE EJEMPLO
-- =============================================

-- Destinos
INSERT INTO Destinos (nombre_destino, ciudad, pais) VALUES
  ('Miami Beach Getaway', 'Miami', 'Estados Unidos'),
  ('Escapada a Madrid', 'Madrid', 'España'),
  ('Aventura en Bariloche', 'San Carlos de Bariloche', 'Argentina');

-- Proveedores
INSERT INTO Proveedores (nombre, tipo_proveedor, contacto) VALUES
  ('Aerolineas Argentinas', 'Aerolínea', 'contacto@aerolineas.com'),
  ('LATAM Airlines', 'Aerolínea', 'info@latam.com');

-- Usuarios de prueba
INSERT INTO Usuarios (usuario, email, password) VALUES
  ('juanperez', 'juan@example.com', '$2y$10$abcdefghijklmnopqrstuv'); -- reemplaza con hash real

-- Clientes de prueba
INSERT INTO Clientes (nombre, apellido, email, telefono, direccion) VALUES
  ('Juan', 'Pérez', 'juan@example.com', '123456789', 'Calle Falsa 123');

-- Servicios: Vuelos
INSERT INTO Servicios (nombre_servicio, descripcion_servicio, tipo_servicio, precio_unitario_base, id_proveedor) VALUES
  ('Vuelo Buenos Aires - Miami', 'Ida y vuelta desde Ezeiza a Miami', 'Transporte', 1200.00, 1),
  ('Vuelo Buenos Aires - Madrid', 'Ida y vuelta desde Ezeiza a Barajas', 'Transporte', 950.00, 1),
  ('Vuelo Buenos Aires - Bariloche', 'Ida y vuelta a Bariloche', 'Transporte', 300.00, 2);

-- Paquetes
INSERT INTO Paquetes (nombre_paquete, descripcion, precio_base, duracion_dias, fecha_inicio_disponibilidad, fecha_fin_disponibilidad, id_destino) VALUES
  ('Playas y Sol en Miami', 'Vacaciones de 5 días en Miami con hotel y vuelo incluidos', 500.00, 5, '2025-07-01', '2025-12-31', 1),
  ('Cultura y Tapas en Madrid', 'Tour de 4 días con hotel + vuelo + city tour', 400.00, 4, '2025-08-15', '2026-01-31', 2),
  ('Naturaleza en Bariloche', '3 noches en cabaña + vuelo + trekking', 350.00, 3, '2025-06-15', '2025-11-30', 3);

-- Detalle de servicios por paquete
INSERT INTO DetallePaqueteServicio (id_paquete, id_servicio, cantidad, notas_especificas_paquete, costo_adicional_paquete) VALUES
  (1, 1, 1, 'Vuelo directo clase económica', 0.00),
  (2, 2, 1, 'Vuelo con escala corta', 0.00),
  (3, 3, 1, 'Vuelo de cabotaje', 0.00);

-- Paquetes de Guía Turístico
INSERT INTO PaquetesGuia (nombre_paquete_guia, descripcion, precio) VALUES
  ('Básico', 'Guía turístico básico con información general', 50.00),
  ('Regular', 'Guía con tours adicionales y atención personalizada', 100.00),
  ('Premium', 'Guía exclusivo con acceso VIP y servicios especiales', 200.00);

-- Tipos de Habitación
INSERT INTO TiposHabitacion (nombre_tipo, precio) VALUES
  ('Simple', 0.00),
  ('Doble', 50.00),
  ('Suite', 150.00);
