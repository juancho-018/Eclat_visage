<?php
/**
 * ECLAT VISAGE — Módulo PHP de Footer Centralizado (modulos/footer.php)
 * Permite que los módulos de PHP incluyan el pie de página único con:
 * include_once __DIR__ . '/../modulos/footer.php'; o include 'modulos/footer.php';
 */
$esCrudDir = (strpos($_SERVER['PHP_SELF'] ?? '', '/CRUD/') !== false || strpos($_SERVER['PHP_SELF'] ?? '', '/crud/') !== false);
$rutaScriptFooter = $esCrudDir ? '../modulos/footer.js' : 'modulos/footer.js';
?>
<!-- MÓDULO FOOTER CENTRALIZADO -->
<div id="footer-modulo"></div>
<script src="<?php echo $rutaScriptFooter; ?>"></script>
