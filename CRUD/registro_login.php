<?php
session_start();
include("conexion.php");

$error_login = "";
$redirect = $_GET['redirect'] ?? ($_POST['redirect'] ?? '');
$mensaje_carrito = ($redirect === 'carrito');

if (isset($_POST['usuario']) && isset($_POST['password'])) {
    $usuario = $conexion->real_escape_string($_POST['usuario']);
    $password = $_POST['password'];

    // Consulta por nombre de usuario en la tabla 'login'
    $sql = "SELECT * FROM login WHERE nombre_login = '$usuario' LIMIT 1";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $hash_bd = $row['contrasena_login'];
        $valido = false;

        // 1. Verificación con BCRYPT
        if (password_verify($password, $hash_bd)) {
            $valido = true;
        } 
        // 2. Compatibilidad retroactiva con contraseñas planas: si coincide, actualizar a BCRYPT
        elseif ($password === $hash_bd) {
            $nuevo_hash_bcrypt = password_hash($password, PASSWORD_BCRYPT);
            $conexion->query("UPDATE login SET contrasena_login = '$nuevo_hash_bcrypt' WHERE id_login = " . $row['id_login']);
            $valido = true;
        }

        if ($valido) {
            $rol_usuario = strtolower(trim($row['rol'] ?? 'cliente'));
            if (empty($rol_usuario)) $rol_usuario = 'cliente';

            // Guardar sesión en PHP
            $_SESSION['usuario'] = $row['nombre_login'];
            $_SESSION['rol'] = $rol_usuario;
            
            // Obtener nombre si existe en clientes
            $nombre_mostrar = $row['nombre_login'];
            $sql_cliente = $conexion->query("SELECT nombre FROM clientes WHERE correo = '$usuario' OR nombre = '$usuario' LIMIT 1");
            if ($sql_cliente && $sql_cliente->num_rows > 0) {
                $c = $sql_cliente->fetch_assoc();
                $nombre_mostrar = $c['nombre'];
            }
            $_SESSION['nombre'] = $nombre_mostrar;

            // Cookie para sincronización con frontend JS
            $cookie_data = json_encode([
                'usuario' => $row['nombre_login'],
                'nombre' => $nombre_mostrar,
                'rol' => $rol_usuario
            ]);
            setcookie('eclat_usuario', $cookie_data, time() + (86400 * 30), '/');

            // Redirección inteligente: si es admin va al CRUD, si viene de carrito va al carrito
            if ($redirect === 'carrito') {
                $destino = '../tienda.html?abrirCarrito=1';
            } elseif ($rol_usuario === 'admin') {
                $destino = 'clientes_crud.php';
            } else {
                $destino = '../index.html';
            }

            echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body style='background:#fdfbf7;'>
            <script>
                localStorage.setItem('eclat_usuario', JSON.stringify(" . $cookie_data . "));
                Swal.fire({
                    title: '¡Bienvenida/o, " . htmlspecialchars($nombre_mostrar) . "!',
                    text: 'Has iniciado sesión exitosamente.',
                    icon: 'success',
                    timer: 1800,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    background: '#fdfbf7',
                    color: '#3d352a'
                }).then(() => {
                    window.location.href = '$destino';
                });
            </script></body></html>";
            exit();
        } else {
            $error_login = "Contraseña incorrecta. Por favor verifica tus datos.";
        }
    } else {
        $error_login = "El usuario no existe. ¿No tienes cuenta? ¡Regístrate abajo!";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .header { display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background-color: #fdfbf7; border-bottom: 1px solid #e6dfd5; }
        .nav { display: flex; align-items: center; gap: 15px; }
        .nav a { text-decoration: none; color: #4a3b32; font-size: 13px; font-weight: 500; text-transform: uppercase; }
        .nav a:hover { color: #b38b59; }
        .dropdown { position: relative; display: none; user-select: none; }
        body.es-admin .dropdown, .dropdown.admin-visible { display: inline-block !important; }
        .dropdown-content { display: none; position: absolute; background-color: #fdfbf7; min-width: 170px; box-shadow: 0px 8px 16px rgba(0,0,0,0.1); z-index: 1000; border: 1px solid #e6dfd5; border-radius: 6px; }
        .dropdown-content a { color: #4a3b32; padding: 10px 15px; text-decoration: none; display: block; font-size: 13px; }
        .dropdown-content a:hover { background-color: #f4efe6; }
        .dropdown.abierto .dropdown-content { display: block !important; }
        .dropdown:not(.cerrado-forzado):hover .dropdown-content { display: block; }
        .dropdown.cerrado-forzado .dropdown-content { display: none !important; }
        .form-container { max-width: 420px; margin: 40px auto; background: #fdfbf7; padding: 35px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e6dfd5; }
        .form-container h2 { font-family: 'Playfair Display', serif; color: #4a3b32; text-align: center; margin-bottom: 20px; font-size: 24px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #5a4a42; font-weight: 500; font-size: 13px; }
        .form-group input { width: 100%; padding: 11px; border: 1px solid #d8c5b2; border-radius: 6px; background: #fff; box-sizing: border-box; font-family: 'Poppins', sans-serif; font-size: 13px; }
        .btn-enviar { background-color: #4a3b32; color: #fff; padding: 12px; border: none; border-radius: 20px; cursor: pointer; font-weight: 500; width: 100%; margin-top: 10px; font-size: 14px; transition: background 0.3s; }
        .btn-enviar:hover { background-color: #5d4a3f; }
        .alerta-error { background-color: #f8d7da; color: #842029; padding: 12px; border-radius: 6px; text-align: center; margin-bottom: 15px; font-size: 13px; border: 1px solid #f5c2c7; }
        .alerta-info-carrito { background-color: #f3ede4; color: #4a3b32; padding: 12px; border-radius: 8px; text-align: center; margin-bottom: 20px; font-size: 13px; border: 1px solid #d8c5b2; line-height: 1.5; }
        .seccion-registro-prompt { margin-top: 25px; padding-top: 20px; border-top: 1px solid #eae2d5; text-align: center; font-size: 13px; color: #6e6456; }
        .btn-ir-registro { display: inline-block; margin-top: 8px; color: #bc9c82; font-weight: 600; text-decoration: none; font-size: 14px; }
        .btn-ir-registro:hover { text-decoration: underline; }
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

            <?php if (isset($_SESSION['rol']) && strtolower(trim($_SESSION['rol'])) === 'admin'): ?>
            <div class="dropdown admin-visible">
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
            <?php endif; ?>
        </nav>
        <div class="header-iconos"><span>🛒 (0)</span></div>
    </header>

    <main>
        <div class="form-container">
            <h2>Iniciar Sesión</h2>

            <?php if ($mensaje_carrito): ?>
                <div class="alerta-info-carrito">
                    🛒 <strong>Acceso al Carrito:</strong><br>
                    Para abrir tu bolsa de compras y guardar tus productos, por favor inicia sesión.
                </div>
            <?php endif; ?>

            <?php if (!empty($error_login)): ?>
                <div class="alerta-error"><?php echo $error_login; ?></div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Error de Acceso',
                            text: '<?php echo addslashes($error_login); ?>',
                            icon: 'error',
                            confirmButtonColor: '#3d352a',
                            background: '#fdfbf7',
                            color: '#3d352a'
                        });
                    });
                </script>
            <?php endif; ?>

            <form action="registro_login.php" method="POST">
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">

                <div class="form-group">
                    <label>Usuario o Correo:</label>
                    <input type="text" name="usuario" required placeholder="Nombre de usuario o correo">
                </div>
                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="password" name="password" required placeholder="Tu contraseña">
                </div>
                <button type="submit" class="btn-enviar">Ingresar</button>
            </form>

            <!-- AVISO DE REGISTRO PARA QUIENES NO TIENEN USUARIO -->
            <div class="seccion-registro-prompt">
                <p>¿No tienes un usuario aún?</p>
                <a href="../registro.html<?php echo ($redirect === 'carrito') ? '?redirect=carrito' : ''; ?>" class="btn-ir-registro">
                    ✦ Puedes registrarte gratis aquí ✦
                </a>
            </div>

            <div style="text-align: center; margin-top: 15px;">
                <a href="../index.html" style="font-size: 12px; color: #8c7a6b; text-decoration: none;">← Volver al inicio</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="copyright" style="text-align: center; padding: 20px; font-size: 12px; color: #8c7a6b;">
            ©️ 2026 Eclat Visage — Todos los derechos reservados.
        </div>
    </footer>

    <script src="../carrito.js"></script>
</body>
</html>