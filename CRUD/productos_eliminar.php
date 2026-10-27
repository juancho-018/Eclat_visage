<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM productos WHERE id_producto = $id";

    if ($conexion->query($sql)) {
        header("Location: productos_crud.php");
        exit();
    } else {
        echo "Error al eliminar el producto: " . $conexion->error;
    }
} else {
    header("Location: productos_crud.php");
    exit();
}
?>