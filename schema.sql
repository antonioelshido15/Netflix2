-- 1. Crear y usar la base de datos
CREATE DATABASE IF NOT EXISTS streaming_db;
USE streaming_db;

-- ==========================================
-- TABLAS INDEPENDIENTES
-- ==========================================

-- Planes Subscripcion
CREATE TABLE PlanesSuscripcion (
    id_plan INT AUTO_INCREMENT PRIMARY KEY,
    nombre_plan VARCHAR(50) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    pantallas_simultaneas INT NOT NULL,
    resolucion_maxima VARCHAR(20) NOT NULL
);

-- Contenidos
CREATE TABLE Contenidos (
    id_contenido INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    tipo_contenido ENUM('Película', 'Serie', 'Documental') NOT NULL,
    clasificacion_edad VARCHAR(10),
    anio_lanzamiento INT
);

-- ==========================================
-- TABLAS DEPENDIENTES
-- ==========================================

-- Cuentas del usuario (Incluye control de acceso administrativo)
CREATE TABLE Cuentas (
    id_cuenta INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_plan INT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    es_admin BOOLEAN DEFAULT FALSE,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado_cuenta ENUM('Activa', 'Suspendida', 'Cancelada') DEFAULT 'Activa',
    FOREIGN KEY (id_plan) REFERENCES PlanesSuscripcion(id_plan)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Pagos suscripcion
CREATE TABLE PagosSuscripcion (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_cuenta INT NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
    metodo_pago VARCHAR(50) NOT NULL,
    estado_pago ENUM('Aprobado', 'Pendiente', 'Declinado') DEFAULT 'Pendiente',
    FOREIGN KEY (id_cuenta) REFERENCES Cuentas(id_cuenta)
    ON DELETE CASCADE ON UPDATE CASCADE
);

-- Perfiles
CREATE TABLE Perfiles (
    id_perfil INT AUTO_INCREMENT PRIMARY KEY,
    id_cuenta INT NOT NULL,
    nombre_perfil VARCHAR(50) NOT NULL,
    avatar VARCHAR(255),
    es_infantil BOOLEAN DEFAULT FALSE,
    idioma_preferido VARCHAR(20) DEFAULT 'Español',
    FOREIGN KEY (id_cuenta) REFERENCES Cuentas(id_cuenta)
    ON DELETE CASCADE ON UPDATE CASCADE
);

-- Historial de reproduccion
CREATE TABLE HistorialReproduccion (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_perfil INT NOT NULL,
    id_contenido INT NOT NULL,
    fecha_visualizacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    marca_tiempo_pausa TIME DEFAULT '00:00:00',
    completado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_perfil) REFERENCES Perfiles(id_perfil)
    ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_contenido) REFERENCES Contenidos(id_contenido)
    ON DELETE CASCADE ON UPDATE CASCADE
);

-- ==========================================
-- DATOS INICIALES
-- ==========================================

-- Planes
INSERT INTO PlanesSuscripcion
    (nombre_plan, precio, pantallas_simultaneas, resolucion_maxima)
VALUES
    ('Básico', 99.00, 1, 'HD'),
    ('Estándar', 169.00, 2, 'Full HD'),
    ('Premium', 239.00, 4, '4K');

-- ==========================================
-- PERFIL ADMIN
-- ==========================================

UPDATE Cuentas SET es_admin = TRUE WHERE email = 'tu_correo@ejemplo.com'; -- Solo para cambiar perfil de admin
-- Cambiar "TRUE" por "FALSE"

-- ==========================================
-- Modificación para suspender usuarios
-- ==========================================

ALTER TABLE Cuentas ADD COLUMN suspendido TINYINT(1) DEFAULT 0;