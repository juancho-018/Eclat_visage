
<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

$sql = "SELECT f.*, c.nombre as cliente_nombre, c.correo as cliente_correo, e.nombre as empleado_nombre
        FROM factura f
        LEFT JOIN clientes c ON f.id_cliente = c.id_cliente
        LEFT JOIN empleados e ON f.id_empleado = e.id_empleado
        ORDER BY f.id_factura DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#faf7f2">
    <title>Gestión de Facturas — Éclat Visage</title>
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
            <h1 style="margin:0;">Gestión de Facturas</h1>
            <a href="facturas_agregar.php" class="btn-agregar" style="margin:0;">+ Registrar Factura Manual</a>
        </div>

        <div class="tabla-contenedor">
            <table id="tabla-facturas" class="tabla-clientes display" style="width:100%;">
                <thead>
                    <tr>
                        <th>Nº Factura</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total ($ COP)</th>
                        <th>Asesor / Empleado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?php echo $row['id_factura']; ?></strong></td>
                                <td><?php echo $row['fecha_factura']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['cliente_nombre'] ?? ('Cliente #' . $row['id_cliente'])); ?></strong>
                                    <?php if (!empty($row['cliente_correo'])): ?>
                                        <div style="font-size:11.5px;color:#8c7a6b;"><?php echo htmlspecialchars($row['cliente_correo']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color:#2e7d32;font-size:14px;">
                                        $<?php echo number_format($row['total'], 2); ?>
                                    </strong>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['empleado_nombre'] ?? ('ID #' . ($row['id_empleado'] ?? 'Web'))); ?>
                                </td>
                                <td class="acciones-cell">
                                    <a href="facturas_editar.php?id=<?php echo $row['id_factura']; ?>" class="btn-accion">Editar</a>
                                    <a href="facturas_eliminar.php?id=<?php echo $row['id_factura']; ?>" class="btn-eliminar">Eliminar</a>
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
            $('#tabla-facturas').DataTable({
                language: {
                    decimal: "",
                    emptyTable: "No hay facturas registradas en la base de datos",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ facturas",
                    infoEmpty: "Mostrando 0 a 0 de 0 facturas",
                    infoFiltered: "(filtrado de un total de _MAX_ facturas)",
                    lengthMenu: "Mostrar _MENU_ facturas por página",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Buscar factura:",
                    zeroRecords: "No se encontraron facturas coincidentes",
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
