<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

$mensaje = "";
if (isset($_POST['guardar'])) {
    $id_proveedor   = intval($_POST['id_proveedor']);
    $nombre_empresa = $conexion->real_escape_string($_POST['nombre_empresa']);
    $contacto       = $conexion->real_escape_string($_POST['contacto']);
    $telefono       = $conexion->real_escape_string($_POST['telefono']);
    $correo         = $conexion->real_escape_string($_POST['correo']);
    $direccion      = $conexion->real_escape_string($_POST['direccion']);

    $sql = "INSERT INTO proveedores (id_proveedor, nombre_empresa, contacto, telefono, correo, direccion) VALUES ($id_proveedor, '$nombre_empresa', '$contacto', '$telefono', '$correo', '$direccion')";
    
    if ($conexion->query($sql)) {
        header("Location: proveedores_crud.php");
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
    <title>Agregar Proveedor — Éclat Visage</title>
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
            <a href="proveedores_crud.php">VOLVER AL CRUD</a>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h2>Agregar Nuevo Proveedor</h2>
            <?php if (!empty($mensaje)) echo "<p style='color:red; text-align:center;'>$mensaje</p>"; ?>
            
            <form action="proveedores_agregar.php" method="POST">
                <div class="form-group">
                    <label>ID Proveedor:</label>
                    <input type="number" name="id_proveedor" required>
                </div>

                <div class="form-group">
                    <label>Nombre Empresa:</label>
                    <input type="text" name="nombre_empresa" required>
                </div>

                <div class="form-group">
                    <label>Contacto:</label>
                    <input type="text" name="contacto" required>
                </div>
                
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono">
                </div>

                <div class="form-group">
                    <label>Correo:</label>
                    <input type="email" name="correo" required>
                </div>
                
                <div class="form-group">
                    <label>Dirección:</label>
                    <input type="text" name="direccion">
                </div>
                
                <button type="submit" name="guardar" class="btn-guardar">Registrar Proveedor</button>
                <a href="proveedores_crud.php" class="btn-cancelar">Cancelar</a>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="copyright">
            ©️ 2026 Eclat Visage — Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>