<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

if (isset($_GET['id'])) {
    $id_empleado = intval($_GET['id']);

    $sql = "DELETE FROM empleados WHERE id_empleado = $id_empleado";

    if ($conexion->query($sql)) {
        header("Location: empleados_crud.php");
        exit();
    } else {
        echo "Error al eliminar el empleado: " . $conexion->error;
        echo "<br><a href='empleados_crud.php'>Regresar</a>";
    }
} else {
    header("Location: empleados_crud.php");
    exit();
}
?>