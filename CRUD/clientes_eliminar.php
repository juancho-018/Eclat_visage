<?php
require_once __DIR__ . '/auth_admin.php';
require_once __DIR__ . '/conexion.php';

// Mostrar una alerta y regresar al listado.
function mostrarAlerta($titulo, $mensaje, $icono = 'warning')
{
    $tituloJS = json_encode($titulo);
    $mensajeJS = json_encode($mensaje);
    $iconoJS = json_encode($icono);

    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Clientes - Éclat Visage</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    title: ' . $tituloJS . ',
                    text: ' . $mensajeJS . ',
                    icon: ' . $iconoJS . ',
                    confirmButtonText: "Entendido",
                    confirmButtonColor: "#403329",
                    background: "#fffdf9",
                    color: "#403329"
                }).then(() => {
                    window.location.href = "clientes_crud.php";
                });
            } else {
                alert(' . $mensajeJS . ');
                window.location.href = "clientes_crud.php";
            }
        </script>
    </body>
    </html>';

    exit;
}

// Validar el ID del cliente.
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: clientes_crud.php");
    exit();
}

try {
    // Verificar si el cliente tiene facturas asociadas.
    $consulta = $conexion->prepare(
        "SELECT COUNT(*) FROM factura WHERE id_cliente = ?"
    );
    $consulta->bind_param("i", $id);
    $consulta->execute();
    $consulta->bind_result($totalFacturas);
    $consulta->fetch();
    $consulta->close();

    // No permitir eliminar clientes con ventas o facturas.
    if ($totalFacturas > 0) {
        mostrarAlerta(
            'No se puede eliminar',
            'Este cliente tiene ventas o facturas asociadas. No es posible eliminarlo porque debe conservarse el historial de ventas.',
            'warning'
        );
    }

    // Eliminar únicamente si no tiene facturas asociadas.
    $eliminar = $conexion->prepare(
        "DELETE FROM clientes WHERE id_cliente = ?"
    );
    $eliminar->bind_param("i", $id);
    $eliminar->execute();
    $eliminar->close();

    header("Location: clientes_crud.php");
    exit();

} catch (mysqli_sql_exception $e) {
    // Protección adicional ante restricciones de claves foráneas.
    if ((int) $e->getCode() === 1451) {
        mostrarAlerta(
            'No se puede eliminar',
            'Este cliente tiene registros relacionados, como ventas o facturas. Debes conservar esos registros.',
            'warning'
        );
    }

    // Registrar el error sin mostrar detalles técnicos al usuario.
    error_log("Error al eliminar cliente: " . $e->getMessage());

    mostrarAlerta(
        'Error al eliminar',
        'Ocurrió un problema al intentar eliminar el cliente. Inténtalo nuevamente.',
        'error'
    );
}
?>