<?php
session_start();
include("CRUD/conexiones.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre    = $conexion->real_escape_string($_POST['nombre'] ?? '');
    $correo    = $conexion->real_escape_string($_POST['correo'] ?? '');
    $usuario   = !empty($_POST['usuario']) ? $conexion->real_escape_string($_POST['usuario']) : $correo;
    $password  = $_POST['password'] ?? '';
    $telefono  = $conexion->real_escape_string($_POST['telefono'] ?? '');
    $direccion = $conexion->real_escape_string($_POST['direccion'] ?? '');
    $redirect  = $_POST['redirect'] ?? '';

    if (empty($password)) {
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body style='background:#fdfbf7;'>
        <script>
            Swal.fire({
                title: 'Campo Obligatorio',
                text: 'La contraseña no puede estar vacía.',
                icon: 'warning',
                confirmButtonColor: '#3d352a'
            }).then(() => window.history.back());
        </script></body></html>";
        exit();
    }

    // 1. Encriptar contraseña con BCRYPT
    $hash_bcrypt = password_hash($password, PASSWORD_BCRYPT);

    // 2. Verificar si el usuario ya existe en la tabla login
    $verificar = $conexion->query("SELECT id_login FROM login WHERE nombre_login = '$usuario'");
    if ($verificar && $verificar->num_rows > 0) {
        $loginUrl = 'CRUD/registro_login.php' . ($redirect === 'carrito' ? '?redirect=carrito' : '');
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body style='background:#fdfbf7;'>
        <script>
            Swal.fire({
                title: 'Usuario ya Registrado',
                text: 'Este usuario o correo ya tiene una cuenta en Éclat Visage. Por favor inicia sesión.',
                icon: 'info',
                confirmButtonText: 'Iniciar Sesión',
                confirmButtonColor: '#3d352a'
            }).then(() => {
                window.location.href = '$loginUrl';
            });
        </script></body></html>";
        exit();
    }

    // 3. Guardar credenciales en la tabla 'login' con hash Bcrypt y rol cliente
    $sql_login = "INSERT INTO login (nombre_login, contrasena_login, rol) VALUES ('$usuario', '$hash_bcrypt', 'cliente')";
    $conexion->query($sql_login);

    // 4. Guardar datos en la tabla 'clientes'
    $sql_cliente = "INSERT INTO clientes (nombre, correo, telefono, direccion, fecha_registro) 
                    VALUES ('$nombre', '$correo', '$telefono', '$direccion', CURDATE())";
    $conexion->query($sql_cliente);

    // 5. Autenticar automáticamente al usuario (Sesión PHP + Cookie para JavaScript)
    $_SESSION['usuario'] = $usuario;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['rol'] = 'cliente';

    $cookie_data = json_encode([
        'usuario' => $usuario,
        'nombre' => $nombre,
        'correo' => $correo,
        'rol' => 'cliente'
    ]);
    setcookie('eclat_usuario', $cookie_data, time() + (86400 * 30), '/');

    // Redirección según de dónde venía el usuario
    $destino = ($redirect === 'carrito') ? 'tienda.html?abrirCarrito=1' : 'index.html';

    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body style='background:#fdfbf7;'>
    <script>
        localStorage.setItem('eclat_usuario', JSON.stringify(" . $cookie_data . "));
        Swal.fire({
            title: '¡Bienvenida/o a Éclat Visage!',
            text: 'Tu cuenta ha sido creada exitosamente. Tu contraseña está asegurada con encriptación Bcrypt.',
            icon: 'success',
            confirmButtonText: 'Continuar a la tienda',
            confirmButtonColor: '#3d352a'
        }).then(() => {
            window.location.href = '$destino';
        });
    </script></body></html>";
    exit();
}
?>