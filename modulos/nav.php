<?php
/**
 * ECLAT VISAGE — Módulo PHP de Navegación Centralizado (modulos/nav.php)
 * Permite que los módulos de PHP incluyan la barra de navegación única con:
 * include_once __DIR__ . '/../modulos/nav.php'; o include 'modulos/nav.php';
 */
$esCrudDir = (strpos($_SERVER['PHP_SELF'] ?? '', '/CRUD/') !== false || strpos($_SERVER['PHP_SELF'] ?? '', '/crud/') !== false);
$rutaScript = $esCrudDir ? '../modulos/nav.js' : 'modulos/nav.js';
?>
<!-- MÓDULO NAV CENTRALIZADO -->
<div id="navbar-modulo"></div>
<script src="<?php echo $rutaScript; ?>"></script>
