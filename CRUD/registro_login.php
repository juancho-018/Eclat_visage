<?php
include("conexion.php");

$error_login = "";

// Verificamos que se haya presionado el botón de enviar
if (isset($_POST['usuario']) && isset($_POST['password'])) {
    $usuario = $conexion->real_escape_string($_POST['usuario']);
    $password = $conexion->real_escape_string($_POST['password']);

    // Consulta a la tabla 'login'
    $sql = "SELECT * FROM login WHERE nombre_login = '$usuario' AND contrasena_login = '$password'";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        // Si coincide, redirige directo a la página de inicio en la raíz
        header("Location: ../index.html");
        exit();
    } else {
        $error_login = "Usuario o contraseña incorrectos. Inténtalo de nuevo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Éclat Visage</title>
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .header { display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background-color: #fdfbf7; border-bottom: 1px solid #e6dfd5; }
        .nav { display: flex; align-items: center; gap: 15px; }
        .nav a { text-decoration: none; color: #4a3b32; font-size: 13px; font-weight: 500; text-transform: uppercase; }
        .nav a:hover { color: #b38b59; }
        .dropdown { position: relative; display: inline-block; }
        .dropdown-content { display: none; position: absolute; background-color: #fdfbf7; min-width: 170px; box-shadow: 0px 8px 16px rgba(0,0,0,0.1); z-index: 1000; border: 1px solid #e6dfd5; border-radius: 6px; }
        .dropdown-content a { color: #4a3b32; padding: 10px 15px; text-decoration: none; display: block; font-size: 13px; }
        .dropdown-content a:hover { background-color: #f4efe6; }
        .dropdown:hover .dropdown-content { display: block; }
        .form-container { max-width: 400px; margin: 50px auto; background: #fdfbf7; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e6dfd5; }
        .form-container h2 { font-family: 'Playfair Display', serif; color: #4a3b32; text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #5a4a42; font-weight: 500; font-size: 14px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #d8c5b2; border-radius: 6px; background: #fff; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        .btn-enviar { background-color: #4a3b32; color: #fff; padding: 10px; border: none; border-radius: 20px; cursor: pointer; font-weight: 500; width: 100%; margin-top: 10px; }
        .btn-enviar:hover { background-color: #5d4a3f; }
        .alerta-error { background-color: #f8d7da; color: #842029; padding: 10px; border-radius: 6px; text-align: center; margin-bottom: 15px; font-size: 13px; border: 1px solid #f5c2c7; }
    </style>
</head>
<body>

    <header class="header">
        <div class="logo" style="font-family: 'Playfair Display', serif; font-size: 20px; color: #4a3b32;">eclat visage<span>✦</span></div>
        <nav class="nav">
            <a href="../index.html">INICIO</a>
            <a href="../tienda.html">VER TODO</a>
            <a href="../nosotros.html">NOSOTROS</a>
            <a href="contacto.php">CONTACTO</a>
            <a href="registro_login.php" style="font-weight: 600; color: #b38b59;">LOGIN</a>
            <a href="https://drive.google.com" target="_blank" rel="noopener noreferrer">DOCUMENTOS</a>

            <div class="dropdown">
                <a href="#" class="dropbtn">CRUD ▾</a>
                <div class="dropdown-content">
                    <a href="clientes_crud.php">Tabla Clientes</a>
                    <a href="proveedores_crud.php">Tabla Proveedores</a>
                    <a href="productos_crud.php">Tabla Productos</a>
                    <a href="categorias_crud.php">Tabla Categorías</a>
                    <a href="inventario_crud.php">Tabla Inventario</a>
                    <a href="facturas_crud.php">Tabla Facturas</a>
                    <a href="empleados_crud.php">Tabla Empleados</a>
                </div>
            </div>
        </nav>
        <div class="header-iconos"><span>🛒 (0)</span></div>
    </header>

    <main>
        <div class="form-container">
            <h2>Iniciar Sesión</h2>

            <?php if (!empty($error_login)): ?>
                <div class="alerta-error"><?php echo $error_login; ?></div>
            <?php endif; ?>

            <form action="registro_login.php" method="POST">
                <div class="form-group">
                    <label>Usuario:</label>
                    <input type="text" name="usuario" required placeholder="Nombre de usuario">
                </div>
                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" name="password" required placeholder="********">
                </div>
                <button type="submit" class="btn-enviar">Ingresar</button>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="copyright" style="text-align: center; padding: 20px; font-size: 12px; color: #8c7a6b;">
            ©️ 2026 Eclat Visage — Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>