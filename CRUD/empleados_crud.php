
<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

$sql = "SELECT * FROM empleados ORDER BY id_empleado DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#faf7f2">
    <title>Gestión de Empleados — Éclat Visage</title>
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
            <h1 style="margin:0;">Gestión de Empleados</h1>
            <a href="empleados_agregar.php" class="btn-agregar" style="margin:0;">+ Agregar Empleado</a>
        </div>

        <div class="tabla-contenedor">
            <table id="tabla-empleados" class="tabla-clientes display" style="width:100%;">
                <thead>
                    <tr>
                        <th>ID Empleado</th>
                        <th>Nombre</th>
                        <th>Cargo</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Fecha Contratación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id_empleado']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['nombre']); ?></strong></td>
                                <td><span class="cargo-badge"><?php echo htmlspecialchars($row['cargo']); ?></span></td>
                                <td><?php echo htmlspecialchars($row['correo']); ?></td>
                                <td><?php echo htmlspecialchars($row['telefono'] ?? ''); ?></td>
                                <td><?php echo $row['fecha_contratacion']; ?></td>
                                <td class="acciones-cell">
                                    <a href="empleados_editar.php?id=<?php echo $row['id_empleado']; ?>" class="btn-accion">Editar</a>
                                    <a href="empleados_eliminar.php?id=<?php echo $row['id_empleado']; ?>" class="btn-eliminar">Eliminar</a>
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
            $('#tabla-empleados').DataTable({
                language: {
                    decimal: "",
                    emptyTable: "No hay empleados registrados en la base de datos",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ empleados",
                    infoEmpty: "Mostrando 0 a 0 de 0 empleados",
                    infoFiltered: "(filtrado de un total de _MAX_ empleados)",
                    lengthMenu: "Mostrar _MENU_ empleados por página",
                    loadingRecords: "Cargando...",
                    processing: "Procesando...",
                    search: "Buscar empleado:",
                    zeroRecords: "No se encontraron empleados coincidentes",
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
