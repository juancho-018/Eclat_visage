<?php
include("conexion.php");

$busqueda = "";
if (isset($_GET['consultar']) && !empty($_GET['query'])) {
    $q = $conexion->real_escape_string($_GET['query']);
    $busqueda = "WHERE p.id_producto LIKE '%$q%' OR p.nombre_producto LIKE '%$q%'";
}

// Consulta corregida usando los nombres exactos de la base de datos (en singular)
$sql = "SELECT p.*, c.nombre_categoria, pr.nombre_empresa 
        FROM productos p 
        LEFT JOIN categoria c ON p.id_categoria = c.id_categoria 
        LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor 
        $busqueda 
        ORDER BY p.id_producto DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .btn-limpiar {
            background-color: #d8c5b2 !important;
            color: #4a3b32 !important;
            border: 1px solid #b8a08c !important;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
            transition: background 0.3s;
        }
        .btn-limpiar:hover {
            background-color: #c9b29c !important;
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="logo">eclat visage<span>✦</span></div>
        <nav class="nav">
            <a href="../index.html">INICIO</a>
            <a href="../tienda.html">VER TODO</a>
            <a href="../mas-vendidos.html">MÁS VENDIDOS</a>
            <a href="../test-piel.html">TEST DE PIEL</a>
            <a href="../diario.html">DIARIO</a>
            <a href="../nosotros.html">NOSOTROS</a>
            
            <div class="dropdown">
                <a href="#" class="dropbtn">CRUD ▾</a>
                <div class="dropdown-content">
                    <a href="clientes_crud.php">Tabla Clientes</a>
                    <a href="proveedores_crud.php">Tabla Proveedores</a>
                    <a href="productos_crud.php">Tabla Productos</a>
                    <a href="categorias_crud.php">Tabla Categorías</a>
                    <a href="inventario_crud.php">Tabla Inventario</a>
                    <a href="facturas_crud.php">Tabla Facturas</a>
                    <a href="empleados_crud.php">Tabla Empleados</a>
                </div>
            </div>
        </nav>
        <div class="header-icons">
            <a href="../login.html" title="Cuenta">👤</a>
            <a href="../carrito.html" title="Carrito">🛒 (0)</a>
        </div>
    </header>

    <main class="panel-cliente">
        <h1>Gestión de Productos</h1>

        <a href="productos_agregar.php" class="btn-agregar">Agregar Producto</a>

        <form action="productos_crud.php" method="GET" class="barra-busqueda">
            <input type="text" name="query" placeholder="ID o Nombre de Producto" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
            <button type="submit" name="consultar" class="btn-consultar">Consultar</button>
            <a href="productos_crud.php" class="btn-limpiar">Limpiar</a>
        </form>

        <div class="tabla-contenedor">
            <table class="tabla-clientes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id_producto']; ?></td>
                                <td><?php echo htmlspecialchars($row['nombre_producto']); ?></td>
                                <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                <td>$<?php echo number_format($row['precio'], 2); ?></td>
                                <td><?php echo $row['stock']; ?></td>
                                <td><?php echo htmlspecialchars($row['nombre_categoria'] ?? 'Sin categoría'); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre_empresa'] ?? 'Sin proveedor'); ?></td>
                                <td class="acciones-cell">
                                    <a href="productos_editar.php?id=<?php echo $row['id_producto']; ?>" class="btn-accion">Editar</a>
                                    <a href="productos_eliminar.php?id=<?php echo $row['id_producto']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás segura de que deseas eliminar este producto?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align:center;">No hay productos registrados en la base de datos.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="footer">
        <div class="copyright">
            ©️ 2026 Eclat Visage — Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>