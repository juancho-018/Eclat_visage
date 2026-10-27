<?php
// Redireccionar al login oficial dentro de CRUD/
$redirect = $_GET['redirect'] ?? '';
$param = $redirect ? "?redirect=" . urlencode($redirect) : "";
header("Location: CRUD/registro_login.php" . $param);
exit();
?>
