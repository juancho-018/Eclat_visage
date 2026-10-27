<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

$resultado = $conexion->query("SELECT * FROM categoria ORDER BY id_categoria DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
            <h1 style="margin: 0;">Gestión de Categorías</h1>
            <a href="categorias_agregar.php" class="btn-agregar" style="margin: 0;">+ Nueva Categoría</a>
        </div>

        <div class="tabla-contenedor">
            <table id="tabla-categorias" class="tabla-clientes display" style="width:100%;">
                <thead>
                    <tr>
                        <th>ID Categoría</th>
                        <th>Nombre Categoría</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id_categoria']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['nombre_categoria']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['descripcion'] ?? ''); ?></td>
                                <td class="acciones-cell">
                                    <a href="categorias_editar.php?id=<?php echo $row['id_categoria']; ?>" class="btn-accion">Editar</a>
                                    <a href="categorias_eliminar.php?id=<?php echo $row['id_categoria']; ?>" class="btn-eliminar">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="footer">
        <div class="copyright">
            © 2026 Eclat Visage — Todos los derechos reservados.
        </div>
    </footer>

    <!-- jQuery, DataTables & Carrito Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="../carrito.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabla-categorias').DataTable({
                language: {
                    decimal: "",
                    emptyTable: "No hay categorías registradas en la base de datos",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ categorías",
                    infoEmpty: "Mostrando 0 a 0 de 0 categorías",
                    infoFiltered: "(filtrado de un total de _MAX_ categorías)",
                    lengthMenu: "Mostrar _MENU_ categorías por página",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Buscar categoría:",
                    zeroRecords: "No se encontraron categorías coincidentes",
                    paginate: {
                        first: "«",
                        last: "»",
                        next: "Siguiente ›",
                        previous: "‹ Anterior"
                    }
                },
                pageLength: 10,
                responsive: true,
                order: [[0, 'desc']]
            });
        });
    </script>
</body>
</html>