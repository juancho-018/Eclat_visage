<?php
require_once __DIR__ . '/auth_admin.php';
include("conexion.php");
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conexion->query("DELETE FROM inventario WHERE id_movimiento = $id");
}
header("Location: inventario_crud.php");
exit();
?>