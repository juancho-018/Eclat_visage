<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

$mensaje = "";
if (isset($_POST['guardar'])) {
    $id_empleado         = intval($_POST['id_empleado']);
    $nombre              = $conexion->real_escape_string($_POST['nombre']);
    $cargo               = $conexion->real_escape_string($_POST['cargo']);
    $correo              = $conexion->real_escape_string($_POST['correo']);
    $telefono            = $conexion->real_escape_string($_POST['telefono']);
    $fecha_contratacion  = $_POST['fecha_contratacion'];

    $sql = "INSERT INTO empleados (id_empleado, nombre, cargo, correo, telefono, fecha_contratacion) 
            VALUES ($id_empleado, '$nombre', '$cargo', '$correo', '$telefono', '$fecha_contratacion')";
    
    if ($conexion->query($sql)) {
        header("Location: empleados_crud.php");
        exit();
    } else {
        $mensaje = "Error al registrar el empleado (verifica que el ID no esté repetido): " . $conexion->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Empleado — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- MÓDULO NAV CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/nav.php'; ?>

    <main>
        <div class="form-container">
            <h2>Agregar Nuevo Empleado</h2>
            <?php if (!empty($mensaje)) echo "<p style='color:red; text-align:center;'>$mensaje</p>"; ?>
            
            <form action="empleados_agregar.php" method="POST">
                <div class="form-group">
                    <label>ID Empleado:</label>
                    <input type="number" name="id_empleado" required>
                </div>

                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" required>
                </div>

                <div class="form-group">
                    <label>Cargo:</label>
                    <input type="text" name="cargo" required>
                </div>

                <div class="form-group">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="correo" required>
                </div>

                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" required>
                </div>

                <div class="form-group">
                    <label>Fecha de Contratación:</label>
                    <input type="date" name="fecha_contratacion" required>
                </div>
                
                <button type="submit" name="guardar" class="btn-guardar">Registrar Empleado</button>
                <a href="empleados_crud.php" class="btn-cancelar">Cancelar</a>
            </form>
        </div>
    </main>

    <!-- FOOTER CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/footer.php'; ?>

</body>
</html>