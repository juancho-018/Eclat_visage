
<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

$sql = "SELECT i.*, p.nombre_producto
        FROM inventario i
        LEFT JOIN productos p ON i.id_producto = p.id_producto
        ORDER BY i.id_movimiento DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#faf7f2">
    <title>Gestión de Inventario — Éclat Visage</title>
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
            <h1 style="margin:0;">Kárdex &amp; Movimientos de Inventario</h1>
            <a href="inventario_agregar.php" class="btn-agregar" style="margin:0;">+ Registrar Movimiento</a>
        </div>

        <div class="tabla-contenedor">
            <table id="tabla-inventario" class="tabla-clientes display" style="width:100%;">
                <thead>
                    <tr>
                        <th>ID Movimiento</th>
                        <th>Producto</th>
                        <th>Tipo Movimiento</th>
                        <th>Cantidad</th>
                        <th>Fecha Movimiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id_movimiento']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['nombre_producto'] ?? ('Producto #' . $row['id_producto'])); ?></strong>
                                    <span style="font-size:11px;color:#8c7a6b;">(ID: <?php echo $row['id_producto']; ?>)</span>
                                </td>
                                <td>
                                    <span style="display:inline-block;padding:3px 8px;border-radius:12px;font-size:12px;font-weight:600;<?php echo strtoupper($row['tipo_movimiento']) === 'ENTRADA' ? 'background:#e8f5e9;color:#2e7d32;' : 'background:#ffebee;color:#c62828;'; ?>">
                                        <?php echo htmlspecialchars($row['tipo_movimiento']); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo $row['cantidad']; ?></strong></td>
                                <td><?php echo $row['fecha_movimiento']; ?></td>
                                <td class="acciones-cell">
                                    <a href="inventario_eliminar.php?id=<?php echo $row['id_movimiento']; ?>" class="btn-eliminar">Eliminar</a>
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
            $('#tabla-inventario').DataTable({
                language: {
                    decimal: "",
                    emptyTable: "No hay movimientos de inventario registrados",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ movimientos",
                    infoEmpty: "Mostrando 0 a 0 de 0 movimientos",
                    infoFiltered: "(filtrado de un total de _MAX_ movimientos)",
                    lengthMenu: "Mostrar _MENU_ movimientos por página",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Buscar movimiento:",
                    zeroRecords: "No se encontraron movimientos coincidentes",
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
