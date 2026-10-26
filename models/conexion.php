<?php
// Desactiva excepciones fatales de PHP 8.1+ para mantener el flujo if/else de la guía
mysqli_report(MYSQLI_REPORT_OFF);

$cn = mysqli_connect("db", "root", "root", "bdalumno");
if (!$cn) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($cn, "utf8mb4");
?>
