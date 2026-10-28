
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
    <meta name="theme-color" content="#faf7f2">
    <title>Gestión de Categorías — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <!-- MÓDULO NAV CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/nav.php'; ?>

    <main class="panel-cliente">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
            <h1 style="margin:0;">Gestión de Categorías</h1>
            <a href="categorias_agregar.php" class="btn-agregar" style="margin:0;">+ Nueva Categoría</a>
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

    <!-- FOOTER CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/footer.php'; ?>

    <!-- JQUERY, DATATABLES Y CARRITO -->
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
