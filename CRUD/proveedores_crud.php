<?php
include("conexion.php");

$busqueda = "";
if (isset($_GET['consultar']) && !empty($_GET['query'])) {
    $q = $conexion->real_escape_string($_GET['query']);
    $busqueda = "WHERE id_proveedor LIKE '%$q%' OR nombre_empresa LIKE '%$q%' OR contacto LIKE '%$q%'";
}

$sql = "SELECT * FROM proveedores $busqueda ORDER BY id_proveedor DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores — Éclat Visage</title>
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
            <a href="../test.html">TEST DE PIEL</a>
            <a href="../blog.html">DIARIO</a>
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
        <div class="header-iconos">
            <span>👤</span>
            <span id="contador-carrito">🛒 (0)</span>
        </div>
    </header>

    <main class="panel-cliente">
        <h1>Gestión de Proveedores</h1>

        <a href="proveedores_agregar.php" class="btn-agregar">Agregar Proveedor</a>

        <form action="proveedores_crud.php" method="GET" class="barra-busqueda">
            <input type="text" name="query" placeholder="ID o Nombre de Empresa" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
            <button type="submit" name="consultar" class="btn-consultar">Consultar</button>
            <a href="proveedores_crud.php" class="btn-limpiar">Limpiar</a>
        </form>

        <div class="tabla-contenedor">
            <table class="tabla-clientes">
                <thead>
                    <tr>
                        <th>ID Proveedor</th>
                        <th>Nombre Empresa</th>
                        <th>Contacto</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id_proveedor']; ?></td>
                                <td><?php echo htmlspecialchars($row['nombre_empresa']); ?></td>
                                <td><?php echo htmlspecialchars($row['contacto']); ?></td>
                                <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($row['correo']); ?></td>
                                <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                                <td class="acciones-cell">
                                    <a href="proveedores_editar.php?id=<?php echo $row['id_proveedor']; ?>" class="btn-accion">Editar</a>
                                    <a href="proveedores_eliminar.php?id=<?php echo $row['id_proveedor']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás segura de que deseas eliminar este proveedor?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">No hay proveedores registrados en la base de datos.</td>
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