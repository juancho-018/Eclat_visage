<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

$id = intval($_GET['id']);
$resultado = $conexion->query("SELECT * FROM inventario WHERE id_movimiento = $id");
$row = $resultado->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_prod = intval($_POST['id_producto']);
    $tipo = $_POST['tipo_movimiento'];
    $cantidad = intval($_POST['cantidad']);
    $fecha = $_POST['fecha_movimiento'];

    $sql = "UPDATE inventario SET id_producto=$id_prod, tipo_movimiento='$tipo', cantidad=$cantidad, fecha_movimiento='$fecha' WHERE id_movimiento=$id";
    if ($conexion->query($sql)) {
        header("Location: inventario_crud.php");
        exit();
    } else {
        $error = "Error al actualizar: " . $conexion->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Inventario - Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body class="body-registro">
    <div class="registro-card">
        <div class="logo">eclat visage<span>✦</span></div>
        <h2>Editar Movimiento ID: <?php echo $id; ?></h2>
        <?php if(isset($error)) { echo "<p style='color:red; font-size:12px;'>$error</p>"; } ?>
        <form action="" method="POST">
            <div class="campo"><label>ID Producto</label><input type="number" name="id_producto" value="<?php echo $row['id_producto']; ?>" required></div>
            <div class="campo"><label>Tipo de Movimiento</label><input type="text" name="tipo_movimiento" value="<?php echo $row['tipo_movimiento']; ?>" required></div>
            <div class="campo"><label>Cantidad</label><input type="number" name="cantidad" value="<?php echo $row['cantidad']; ?>" required></div>
            <div class="campo"><label>Fecha de Movimiento</label><input type="date" name="fecha_movimiento" value="<?php echo $row['fecha_movimiento']; ?>" required></div>
            <button type="submit" class="btn-registro">ACTUALIZAR MOVIMIENTO</button>
        </form>
        <br><a href="inventario_crud.php" style="font-size:12px; color:#666; text-decoration:none;">← Volver</a>
    </div>
</body>
</html>