<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

$mensaje = "";
if (isset($_POST['guardar'])) {
    $id_cliente = intval($_POST['id_cliente']);
    $nombre     = $conexion->real_escape_string($_POST['nombre']);
    $correo     = $conexion->real_escape_string($_POST['correo']);
    $telefono   = $conexion->real_escape_string($_POST['telefono']);
    $direccion  = $conexion->real_escape_string($_POST['direccion']);

    $sql = "INSERT INTO clientes (id_cliente, nombre, correo, telefono, direccion) VALUES ($id_cliente, '$nombre', '$correo', '$telefono', '$direccion')";
    
    if ($conexion->query($sql)) {
        header("Location: clientes_crud.php");
        exit();
    } else {
        $mensaje = "Error al registrar (es posible que el ID ya exista): " . $conexion->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cliente — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- MÓDULO NAV CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/nav.php'; ?>

    <main>
        <div class="form-container">
            <h2>Agregar Nuevo Cliente</h2>
            <?php if (!empty($mensaje)) echo "<p style='color:red; text-align:center;'>$mensaje</p>"; ?>
            
            <form action="clientes_agregar.php" method="POST">
                <div class="form-group">
                    <label>ID Cliente:</label>
                    <input type="number" name="id_cliente" >
                </div>

                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label>Correo:</label>
                    <input type="email" name="correo" required>
                </div>
                
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono">
                </div>
                
                <div class="form-group">
                    <label>Dirección:</label>
                    <input type="text" name="direccion">
                </div>
                
                <button type="submit" name="guardar" class="btn-guardar">Registrar Cliente</button>
                <a href="clientes_crud.php" class="btn-cancelar">Cancelar</a>
            </form>
        </div>
    </main>

    <!-- FOOTER CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/footer.php'; ?>

</body>
</html>