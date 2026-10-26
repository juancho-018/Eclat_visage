<?php
include("conexion.php");

if (isset($_GET['id'])) {
    $id_factura = intval($_GET['id']);

    // Sentencia SQL para eliminar la factura
    $sql = "DELETE FROM factura WHERE id_factura = $id_factura";

    if ($conexion->query($sql)) {
        header("Location: facturas_crud.php");
        exit();
    } else {
        echo "Error al eliminar la factura: " . $conexion->error;
        echo "<br><a href='facturas_crud.php'>Regresar</a>";
    }
} else {
    header("Location: facturas_crud.php");
    exit();
}
?>