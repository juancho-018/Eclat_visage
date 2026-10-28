<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

$mensaje = "";
if (isset($_POST['guardar'])) {
    $id_factura   = intval($_POST['id_factura']);
    $fecha_factura= $_POST['fecha_factura'];
    $total        = floatval($_POST['total']);
    $id_cliente   = intval($_POST['id_cliente']);
    $id_empleado  = intval($_POST['id_empleado']);

    $sql = "INSERT INTO factura (id_factura, fecha_factura, total, id_cliente, id_empleado) 
            VALUES ($id_factura, '$fecha_factura', $total, $id_cliente, $id_empleado)";
    
    if ($conexion->query($sql)) {
        header("Location: facturas_crud.php");
        exit();
    } else {
        $mensaje = "Error al registrar la factura (verifica que el ID no esté repetido o que el cliente/empleado existan): " . $conexion->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Factura — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- MÓDULO NAV CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/nav.php'; ?>

    <main>
        <div class="form-container">
            <h2>Registrar Nueva Factura</h2>
            <?php if (!empty($mensaje)) echo "<p style='color:red; text-align:center;'>$mensaje</p>"; ?>
            
            <form action="facturas_agregar.php" method="POST">
                <div class="form-group">
                    <label>ID Factura:</label>
                    <input type="number" name="id_factura" required>
                </div>

                <div class="form-group">
                    <label>Fecha:</label>
                    <input type="date" name="fecha_factura" required>
                </div>

                <div class="form-group">
                    <label>Total ($):</label>
                    <input type="number" step="0.01" name="total" required>
                </div>

                <div class="form-group">
                    <label>ID Cliente:</label>
                    <input type="number" name="id_cliente" required>
                </div>

                <div class="form-group">
                    <label>ID Empleado:</label>
                    <input type="number" name="id_empleado" required>
                </div>
                
                <button type="submit" name="guardar" class="btn-guardar">Registrar Factura</button>
                <a href="facturas_crud.php" class="btn-cancelar">Cancelar</a>
            </form>
        </div>
    </main>

    <!-- FOOTER CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/footer.php'; ?>

</body>
</html>