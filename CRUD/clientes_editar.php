<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

// 1. Si se envió el formulario para actualizar
if (isset($_POST['actualizar'])) {
    $id        = intval($_POST['id_cliente']);
    $nombre    = $conexion->real_escape_string($_POST['nombre']);
    $correo    = $conexion->real_escape_string($_POST['correo']);
    $telefono  = $conexion->real_escape_string($_POST['telefono']);
    $direccion = $conexion->real_escape_string($_POST['direccion']);

    $sql_update = "UPDATE clientes SET nombre='$nombre', correo='$correo', telefono='$telefono', direccion='$direccion' WHERE id_cliente=$id";
    
    if ($conexion->query($sql_update)) {
        header("Location: clientes_crud.php");
        exit();
    } else {
        $error = "Error al actualizar: " . $conexion->error;
    }
}

// 2. Cargar los datos actuales del cliente usando el ID de la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $resultado = $conexion->query("SELECT * FROM clientes WHERE id_cliente = $id");
    if ($resultado->num_rows > 0) {
        $cliente = $resultado->fetch_assoc();
    } else {
        header("Location: clientes_crud.php");
        exit();
    }
} else {
    header("Location: clientes_crud.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .form-container {
            max-width: 500px;
            margin: 50px auto;
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
            <a href="clientes_crud.php">VOLVER AL CRUD</a>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h2>Editar Cliente</h2>
            <?php if (isset($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>
            
            <form action="clientes_editar.php" method="POST">
                <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">
                
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Correo:</label>
                    <input type="email" name="correo" value="<?php echo htmlspecialchars($cliente['correo']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>">
                </div>
                
                <div class="form-group">
                    <label>Dirección:</label>
                    <input type="text" name="direccion" value="<?php echo htmlspecialchars($cliente['direccion']); ?>">
                </div>
                
                <button type="submit" name="actualizar" class="btn-guardar">Guardar Cambios</button>
                <a href="clientes_crud.php" class="btn-cancelar">Cancelar</a>
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