-- ========================================================
-- BASE DE DATOS COMPLETA: eclat_visage
-- Proyecto: Éclat Visage - Skincare & Nutrición Facial
-- Compatible con: MySQL 5.7+, MySQL 8.0+, MariaDB, phpMyAdmin, XAMPP, WampServer
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 1. CREACIÓN DE LA BASE DE DATOS
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `eclat_visage` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `eclat_visage`;

-- --------------------------------------------------------
-- 2. ELIMINACIÓN PREVENTIVA DE TABLAS (ORDEN SEGURO)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `detalle_factura`;
DROP TABLE IF EXISTS `inventario`;
DROP TABLE IF EXISTS `factura`;
DROP TABLE IF EXISTS `productos`;
DROP TABLE IF EXISTS `categoria`;
DROP TABLE IF EXISTS `proveedores`;
DROP TABLE IF EXISTS `empleados`;
DROP TABLE IF EXISTS `clientes`;
DROP TABLE IF EXISTS `login`;

-- --------------------------------------------------------
-- 3. CREACIÓN DE TABLAS
-- --------------------------------------------------------

-- Tabla: login (Autenticación de usuarios/administradores)
CREATE TABLE `login` (
  `id_login` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_login` varchar(100) NOT NULL,
  `contrasena_login` varchar(255) NOT NULL,
  `rol` varchar(30) NOT NULL DEFAULT 'cliente',
  PRIMARY KEY (`id_login`),
  UNIQUE KEY `uk_nombre_login` (`nombre_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: clientes (Registro de compradores y clientes)
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `fecha_registro` date NOT NULL DEFAULT (CURRENT_DATE),
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: categoria (Categorías de productos cosméticos)
CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: proveedores (Proveedores y laboratorios de insumos)
CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_empresa` varchar(150) NOT NULL,
  `contacto` varchar(150) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: empleados (Personal administrativo, dermatólogos y ventas)
CREATE TABLE `empleados` (
  `id_empleado` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `fecha_contratacion` date NOT NULL DEFAULT (CURRENT_DATE),
  PRIMARY KEY (`id_empleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: productos (Catálogo de fórmulas y cosméticos)
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_producto` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `id_categoria` int(11) DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_producto`),
  KEY `fk_productos_categoria` (`id_categoria`),
  KEY `fk_productos_proveedores` (`id_proveedor`),
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_productos_proveedores` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: inventario (Kárdex y control de entradas/salidas de stock)
CREATE TABLE `inventario` (
  `id_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `tipo_movimiento` varchar(50) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_movimiento` date NOT NULL DEFAULT (CURRENT_DATE),
  PRIMARY KEY (`id_movimiento`),
  KEY `fk_inventario_producto` (`id_producto`),
  CONSTRAINT `fk_inventario_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: factura (Facturación de compras y pedidos)
CREATE TABLE `factura` (
  `id_factura` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_factura` date NOT NULL DEFAULT (CURRENT_DATE),
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `id_cliente` int(11) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_factura`),
  KEY `fk_factura_cliente` (`id_cliente`),
  KEY `fk_factura_empleado` (`id_empleado`),
  CONSTRAINT `fk_factura_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_factura_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: detalle_factura (Productos asociados a cada factura)
CREATE TABLE `detalle_factura` (
  `id_producto` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_producto`,`id_factura`),
  KEY `fk_detalle_factura` (`id_factura`),
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_factura` FOREIGN KEY (`id_factura`) REFERENCES `factura` (`id_factura`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. INSERCIÓN DE DATOS INICIALES (SEMILLA / DEMO)
-- --------------------------------------------------------

-- 4.1 Usuarios de acceso (Login con contraseñas encriptadas con BCRYPT y roles)
-- Contraseñas originales: admin -> admin123 | eclat -> eclat2026 | usuario@email.com -> 123456
INSERT INTO `login` (`id_login`, `nombre_login`, `contrasena_login`, `rol`) VALUES
(1, 'admin', '$2y$10$V2QVQGveePQH2R.KUqW7ceiTC74jwZ8R6JLOO4./2IF8sJ9jEU0O.', 'admin'),
(2, 'eclat', '$2y$10$Ry6clswEdZZTRvXnwetRvO4NdjBIqM7z1EqrFSLBXSFgsBAjY2o8C', 'admin'),
(3, 'usuario@email.com', '$2y$10$xqo0hcw8NkQrMw/AIiRjU..uj4d529Zqp59TkaaFXHFt8m7.XhsGe', 'cliente');

-- 4.2 Categorías de cosméticos
INSERT INTO `categoria` (`id_categoria`, `nombre_categoria`, `descripcion`) VALUES
(1, 'Sérums & Elíxires', 'Tratamientos concentrados con ingredientes activos para hidratación, antiedad y luminosidad.'),
(2, 'Cremas & Bálsamos', 'Emulsiones nutritivas y fórmulas de reparación para la barrera cutánea.'),
(3, 'Limpiadores & Tónicos', 'Higiene facial botánica que limpia y restablece el balance de pH.'),
(4, 'Contorno de Ojos', 'Cuidado específico para descongestionar bolsas, ojeras y líneas de expresión.'),
(5, 'Kits & Mascarillas', 'Tratamientos semanales intensivos y rutinas faciales completas.');

-- 4.3 Proveedores
INSERT INTO `proveedores` (`id_proveedor`, `nombre_empresa`, `contacto`, `telefono`, `correo`, `direccion`) VALUES
(1, 'Laboratorios Botánicos de los Andes', 'Dra. Elena Silva', '3157894512', 'contacto@botanicosandes.com', 'Cra. 45 # 128-20, Bogotá'),
(2, 'Dermacare Cosmética Natural', 'Carlos Mendoza', '3104561289', 'ventas@dermacare.com', 'Av. Poblado # 15-40, Medellín'),
(3, 'EcoSkin & Esencias Puras', 'Mariana Gómez', '3189632587', 'suministros@ecoskin.co', 'Calle 10 # 32-15, Cali'),
(4, 'BioBelleza Distribuciones', 'Andrés Rincón', '3218529630', 'pedidos@biobelleza.com', 'Cra. 53 # 74-88, Barranquilla');

-- 4.4 Empleados
INSERT INTO `empleados` (`id_empleado`, `nombre`, `cargo`, `correo`, `telefono`, `fecha_contratacion`) VALUES
(1, 'Camila Restrepo', 'Gerente General', 'camila@eclatvisage.com', '3112233445', '2025-01-15'),
(2, 'Valentina Morales', 'Especialista Dermatológica', 'valentina@eclatvisage.com', '3123344556', '2025-02-01'),
(3, 'Sebastián Herrera', 'Asesor de Ventas & Caja', 'sebastian@eclatvisage.com', '3134455667', '2025-03-10'),
(4, 'Daniela Torres', 'Coordinadora de Inventario', 'daniela@eclatvisage.com', '3145566778', '2025-04-05');

-- 4.5 Clientes
INSERT INTO `clientes` (`id_cliente`, `nombre`, `correo`, `telefono`, `direccion`, `fecha_registro`) VALUES
(1, 'Laura Sofía Castro', 'laura.castro@gmail.com', '3001234567', 'Calle 85 # 14-22, Bogotá', '2026-01-10'),
(2, 'Juan David Pardo', 'jdpardo@outlook.com', '3159876543', 'Transversal 39 # 72-10, Medellín', '2026-01-18'),
(3, 'María Paula Ortiz', 'maria.ortiz@hotmail.com', '3205556677', 'Carrera 66 # 9-45, Cali', '2026-02-05'),
(4, 'Carolina Vega', 'carolina.vega@gmail.com', '3184443322', 'Calle 79 # 51B-30, Barranquilla', '2026-02-20');

-- 4.6 Productos (Catálogo real de la tienda)
INSERT INTO `productos` (`id_producto`, `nombre_producto`, `descripcion`, `precio`, `stock`, `id_categoria`, `id_proveedor`) VALUES
(1, 'Sérum Hidratante Profundo', 'Aporte intensivo de agua celular para eliminar la tirantez de la piel.', 18000.00, 25, 1, 1),
(2, 'Elíxir Iluminador Vitamina C', 'Antioxidante potente que despierta el brillo natural y unifica el tono.', 24000.00, 18, 1, 2),
(3, 'Sérum Renovador Nocturno', 'Fórmula suave que suaviza líneas de expresión y texturas ásperas.', 32000.00, 15, 1, 1),
(4, 'Sérum de Niacinamida 10%', 'Regulador avanzado de poros dilatados y manchas por imperfecciones.', 25500.00, 20, 1, 3),
(5, 'Contorno de Ojos', 'Nutrición fina para atenuar bolsas, ojeras y signos de fatiga visual.', 22000.00, 14, 4, 2),
(6, 'Tónico Botánico Calmante', 'Bruma herbolaria que restablece el pH ideal justo tras la limpieza.', 14000.00, 30, 3, 3),
(7, 'Crema Nutritiva de Día', 'Emulsión ligera para proteger la barrera cutánea frente al clima.', 28000.00, 22, 2, 1),
(8, 'Crema de Brillo Vitamina C', 'Aporte culinario antioxidante para revitalizar pieles cansadas u opacas.', 26000.00, 16, 2, 2),
(9, 'Bálsamo Reparador Intenso', 'Nutrición profunda para pieles extremadamente secas o maduras.', 30000.00, 12, 2, 4);

-- 4.7 Facturas
INSERT INTO `factura` (`id_factura`, `fecha_factura`, `total`, `id_cliente`, `id_empleado`) VALUES
(1, '2026-02-25', 42000.00, 1, 3),
(2, '2026-03-02', 58000.00, 2, 3),
(3, '2026-03-15', 30000.00, 3, 2),
(4, '2026-03-20', 74000.00, 4, 3);

-- 4.8 Inventario
INSERT INTO `inventario` (`id_movimiento`, `id_producto`, `tipo_movimiento`, `cantidad`, `fecha_movimiento`) VALUES
(1, 1, 'Entrada', 30, '2026-01-10'),
(2, 2, 'Entrada', 25, '2026-01-10'),
(3, 3, 'Entrada', 20, '2026-01-12'),
(4, 1, 'Salida', 5, '2026-02-25'),
(5, 2, 'Salida', 7, '2026-03-02');

-- --------------------------------------------------------
-- 5. FINALIZACIÓN Y HABILITACIÓN DE CHEQUEOS
-- --------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
