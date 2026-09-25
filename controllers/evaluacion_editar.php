<?php
/**
 * Editar Evaluación — NADIE puede editar una vez enviada
 * Mantiene el enlace pero avisa que no se puede modificar
 */
require_once __DIR__ . '/../config/sesion.php';

echo "<script>alert('La evaluación ya fue registrada y no se puede modificar');history.back();</script>";
exit;