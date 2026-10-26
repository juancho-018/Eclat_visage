<?php
include("conexion.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM categoria WHERE id_categoria = $id";

    if ($conexion->query($sql)) {
        header("Location: categorias_crud.php");
        exit();
    } else {
        echo "Error al eliminar la categoría: " . $conexion->error;
    }
} else {
    header("Location: categorias_crud.php");
    exit();
}
?>