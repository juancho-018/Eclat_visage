<?php
session_start();
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Eliminar cookie del usuario en todas las rutas posibles
setcookie("eclat_usuario", "", time() - 3600, "/");
setcookie("eclat_usuario", "", time() - 3600, "/CRUD");
setcookie("eclat_usuario", "", time() - 3600);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión Finalizada — Éclat Visage</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #fdfbf7; font-family: 'Poppins', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
    </style>
</head>
<body>
    <script>
        localStorage.removeItem('eclat_usuario');
        document.cookie = "eclat_usuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "eclat_usuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/CRUD;";
        document.cookie = "eclat_usuario=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";

        const irAInicio = () => { window.location.href = 'index.html'; };
        const timerSeguridad = setTimeout(irAInicio, 1500);

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Sesión Finalizada',
                text: 'Has cerrado sesión exitosamente. ¡Esperamos verte pronto!',
                icon: 'success',
                timer: 1200,
                timerProgressBar: true,
                showConfirmButton: false,
                background: '#fdfbf7',
                color: '#3d352a'
            }).then(() => {
                clearTimeout(timerSeguridad);
                irAInicio();
            });
        } else {
            irAInicio();
        }
    </script>
</body>
</html>
