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
    <style>
        .form-container {
            max-width: 500px;
            margin: 40px auto;
            background: #fdfbf7;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e6dfd5;
        }
        .form-container h2 {
            font-family: 'Playfair Display', serif;
            color: #4a3b32;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #5a4a42;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #d8c5b2;
            border-radius: 6px;
            background: #fff;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        .btn-guardar {
            background-color: #4a3b32;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            width: 100%;
            margin-top: 10px;
            transition: background 0.3s;
        }
        .btn-guardar:hover {
            background-color: #5d4a3f;
        }
        .btn-cancelar {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #8c7a6b;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-cancelar:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="logo">eclat visage<span>✦</span></div>
        <nav class="nav">
            <a href="../index.html">INICIO</a>
            <a href="empleados_crud.php">VOLVER AL CRUD</a>
        </nav>
    </header>

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

    <footer class="footer">
        <div class="copyright">
            © 2026 Eclat Visage — Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>