<?php
include("conexion.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Consulta para eliminar el registro
    $sql = "DELETE FROM clientes WHERE id_cliente = $id";
    
    if ($conexion->query($sql)) {
        header("Location: clientes_crud.php");
        exit();
    } else {
        echo "Error al eliminar el registro: " . $conexion->error;
    }
} else {
    header("Location: clientes_crud.php");
    exit();
}
?>