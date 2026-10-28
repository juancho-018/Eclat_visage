<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

$mensaje = "";

if (isset($_GET['id'])) {
    $id_empleado = intval($_GET['id']);
    $resultado = $conexion->query("SELECT * FROM empleados WHERE id_empleado = $id_empleado");
    
    if ($resultado->num_rows > 0) {
        $empleado = $resultado->fetch_assoc();
    } else {
        header("Location: empleados_crud.php");
        exit();
    }
}

if (isset($_POST['actualizar'])) {
    $id_empleado         = intval($_POST['id_empleado']);
    $nombre              = $conexion->real_escape_string($_POST['nombre']);
    $cargo               = $conexion->real_escape_string($_POST['cargo']);
    $correo              = $conexion->real_escape_string($_POST['correo']);
    $telefono            = $conexion->real_escape_string($_POST['telefono']);
    $fecha_contratacion  = $_POST['fecha_contratacion'];

    $sql = "UPDATE empleados SET 
            nombre = '$nombre', 
            cargo = '$cargo', 
            correo = '$correo', 
            telefono = '$telefono', 
            fecha_contratacion = '$fecha_contratacion' 
            WHERE id_empleado = $id_empleado";

    if ($conexion->query($sql)) {
        header("Location: empleados_crud.php");
        exit();
    } else {
        $mensaje = "Error al actualizar el empleado: " . $conexion->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- MÓDULO NAV CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/nav.php'; ?>

    <main>
        <div class="form-container">
            <h2>Editar Empleado: <?php echo htmlspecialchars($empleado['nombre']); ?></h2>
            <?php if (!empty($mensaje)) echo "<p style='color:red; text-align:center;'>$mensaje</p>"; ?>
            
            <form action="empleados_editar.php" method="POST">
                <input type="hidden" name="id_empleado" value="<?php echo $empleado['id_empleado']; ?>">

                <div class="form-group">
                    <label>ID Empleado (No modificable):</label>
                    <input type="number" value="<?php echo $empleado['id_empleado']; ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($empleado['nombre']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Cargo:</label>
                    <input type="text" name="cargo" value="<?php echo htmlspecialchars($empleado['cargo']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="correo" value="<?php echo htmlspecialchars($empleado['correo']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($empleado['telefono']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Fecha de Contratación:</label>
                    <input type="date" name="fecha_contratacion" value="<?php echo $empleado['fecha_contratacion']; ?>" required>
                </div>
                
                <button type="submit" name="actualizar" class="btn-guardar">Guardar Cambios</button>
                <a href="empleados_crud.php" class="btn-cancelar">Cancelar</a>
            </form>
        </div>
    </main>

    <!-- FOOTER CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/footer.php'; ?>

</body>
</html>