<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id_movimiento']);
    $id_prod = intval($_POST['id_producto']);
    $tipo = $_POST['tipo_movimiento'];
    $cantidad = intval($_POST['cantidad']);
    $fecha = $_POST['fecha_movimiento'];

    $sql = "INSERT INTO inventario (id_movimiento, id_producto, tipo_movimiento, cantidad, fecha_movimiento) VALUES ($id, $id_prod, '$tipo', $cantidad, '$fecha')";
    if ($conexion->query($sql)) {
        header("Location: inventario_crud.php");
        exit();
    } else {
        $error = "Error al registrar (¿El ID de movimiento ya existe?): " . $conexion->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Inventario - Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body class="body-registro">
    <div class="registro-card">
        <div class="logo">eclat visage<span>✦</span></div>
        <h2>Nuevo Movimiento</h2>
        <?php if(isset($error)) { echo "<p style='color:red; font-size:12px;'>$error</p>"; } ?>
        <form action="" method="POST">
            <div class="campo"><label>ID Movimiento</label><input type="number" name="id_movimiento" required></div>
            <div class="campo"><label>ID Producto</label><input type="number" name="id_producto" required></div>
            <div class="campo"><label>Tipo de Movimiento</label><input type="text" name="tipo_movimiento" placeholder="Ej: Entrada / Salida" required></div>
            <div class="campo"><label>Cantidad</label><input type="number" name="cantidad" required></div>
            <div class="campo"><label>Fecha de Movimiento</label><input type="date" name="fecha_movimiento" required></div>
            <button type="submit" class="btn-registro">GUARDAR MOVIMIENTO</button>
        </form>
        <br><a href="inventario_crud.php" style="font-size:12px; color:#666; text-decoration:none;">← Volver</a>
    </div>
</body>
</html>