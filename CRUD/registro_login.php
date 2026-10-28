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
</head>
<body>

    <!-- MÓDULO NAV CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/nav.php'; ?>

    <main>
        <div class="form-container">
            <h2>Iniciar Sesión</h2>

            <?php if ($mensaje_carrito): ?>
                <div class="alerta-info-carrito">
                    <strong>Acceso al Carrito:</strong><br>
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
                    Puedes registrarte gratis aquí
                </a>
            </div>

            <div style="text-align: center; margin-top: 15px;">
                <a href="../index.html" style="font-size: 12px; color: #8c7a6b; text-decoration: none;">← Volver al inicio</a>
            </div>
        </div>
    </main>

    <!-- FOOTER CENTRALIZADO -->
    <?php include_once __DIR__ . '/../modulos/footer.php'; ?>

    <script src="../carrito.js"></script>
</body>
</html>