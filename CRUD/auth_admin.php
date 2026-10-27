<?php
/**
 * ECLAT VISAGE — Control de Acceso CRUD
 * Solo los usuarios con rol 'admin' pueden acceder a las vistas y operaciones del CRUD.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$esAdmin = false;

// 1. Validar por sesión de PHP
if (isset($_SESSION['usuario']) && isset($_SESSION['rol']) && strtolower(trim($_SESSION['rol'])) === 'admin') {
    $esAdmin = true;
} 
// 2. Validar por Cookie persistente sincronizada
elseif (isset($_COOKIE['eclat_usuario'])) {
    $cookieData = json_decode($_COOKIE['eclat_usuario'], true);
    if ($cookieData && isset($cookieData['rol']) && strtolower(trim($cookieData['rol'])) === 'admin') {
        $esAdmin = true;
        $_SESSION['usuario'] = $cookieData['usuario'];
        $_SESSION['nombre'] = $cookieData['nombre'] ?? $cookieData['usuario'];
        $_SESSION['rol'] = 'admin';
    }
}

// 3. Si no es administrador, bloquear acceso y redirigir
if (!$esAdmin) {
    $estaAutenticado = isset($_SESSION['usuario']);
    $nombreUsuario = $_SESSION['nombre'] ?? ($_SESSION['usuario'] ?? 'invitado');
    
    $mensaje = $estaAutenticado 
        ? "Hola $nombreUsuario. Tu cuenta tiene rol de 'Cliente'. El módulo de administración (CRUD) está restringido únicamente para usuarios con rol de Administrador."
        : "Debes iniciar sesión con una cuenta de Administrador para acceder al panel de gestión (CRUD).";

    $botonTexto = $estaAutenticado ? "Volver a la Tienda" : "Iniciar Sesión como Admin";
    $redireccion = $estaAutenticado ? "../tienda.html" : "registro_login.php";

    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Acceso Restringido — Éclat Visage</title>
        <link rel='stylesheet' href='../estilos.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body style='background:#fdfbf7; font-family:\"Poppins\", sans-serif;'>
        <script>
            Swal.fire({
                title: 'Acceso Restringido (Solo Admin)',
                text: " . json_encode($mensaje, JSON_UNESCAPED_UNICODE) . ",
                icon: 'warning',
                confirmButtonText: " . json_encode($botonTexto, JSON_UNESCAPED_UNICODE) . ",
                confirmButtonColor: '#3d352a',
                background: '#fdfbf7',
                color: '#3d352a',
                allowOutsideClick: false
            }).then(() => {
                window.location.href = '$redireccion';
            });
        </script>
    </body>
    </html>";
    exit();
}
?>
