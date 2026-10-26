<?php
include("conexion.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "DELETE FROM proveedores WHERE id_proveedor = $id";
    
    if ($conexion->query($sql)) {
        header("Location: proveedores_crud.php");
        exit();
    } else {
        echo "Error al eliminar el registro: " . $conexion->error;
    }
} else {
    header("Location: proveedores_crud.php");
    exit();
}
?>