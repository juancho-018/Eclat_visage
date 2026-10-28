<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

if (isset($_POST['actualizar'])) {
    $id             = intval($_POST['id_proveedor']);
    $nombre_empresa = $conexion->real_escape_string($_POST['nombre_empresa']);
    $contacto       = $conexion->real_escape_string($_POST['contacto']);
    $telefono       = $conexion->real_escape_string($_POST['telefono']);
    $correo         = $conexion->real_escape_string($_POST['correo']);
    $direccion      = $conexion->real_escape_string($_POST['direccion']);

    $sql_update = "UPDATE proveedores SET nombre_empresa='$nombre_empresa', contacto='$contacto', telefono='$telefono', correo='$correo', direccion='$direccion' WHERE id_proveedor=$id";
    
    if ($conexion->query($sql_update)) {
        header("Location: proveedores_crud.php");
        exit();
    } else {
        $error = "Error al actualizar: " . $conexion->error;
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $resultado = $conexion->query("SELECT * FROM proveedores WHERE id_proveedor = $id");
    if ($resultado->num_rows > 0) {
        $proveedor = $resultado->fetch_assoc();
    } else {
        header("Location: proveedores_crud.php");
        exit();
    }
} else {
    header("Location: proveedores_crud.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- MÓDULO NAV CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/nav.php'; ?>

    <main>
        <div class="form-container">
            <h2>Editar Proveedor</h2>
            <?php if (isset($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>
            
            <form action="proveedores_editar.php?id=<?php echo $proveedor['id_proveedor']; ?>" method="POST">
                <input type="hidden" name="id_proveedor" value="<?php echo $proveedor['id_proveedor']; ?>">
                
                <div class="form-group">
                    <label>Nombre Empresa:</label>
                    <input type="text" name="nombre_empresa" value="<?php echo htmlspecialchars($proveedor['nombre_empresa']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Contacto:</label>
                    <input type="text" name="contacto" value="<?php echo htmlspecialchars($proveedor['contacto']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($proveedor['telefono']); ?>">
                </div>

                <div class="form-group">
                    <label>Correo:</label>
                    <input type="email" name="correo" value="<?php echo htmlspecialchars($proveedor['correo']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Dirección:</label>
                    <input type="text" name="direccion" value="<?php echo htmlspecialchars($proveedor['direccion']); ?>">
                </div>
                
                <button type="submit" name="actualizar" class="btn-guardar">Guardar Cambios</button>
                <a href="proveedores_crud.php" class="btn-cancelar">Cancelar</a>
            </form>
        </div>
    </main>

    <!-- FOOTER CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/footer.php'; ?>

</body>
</html>