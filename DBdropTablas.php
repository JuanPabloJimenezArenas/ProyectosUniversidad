<?php
require_once __DIR__ . '/../../../data/config.php';

$dblink = mysqli_connect("dbserver", "grupo" . GRUPO, DB_pasword, "db_grupo" . GRUPO);

if (!$dblink) {
    die("Conexion fallida: " . mysqli_connect_error());
}

mysqli_query($dblink, "SET FOREIGN_KEY_CHECKS = 0");

$sql = "DROP TABLE IF EXISTS
    `campeon_especie`,
    `campeon_region`,
    `objeto_recetas`,
    `campeon`,
    `especie`,
    `objeto`,
    `region`,
    `runa`,
    `habilidad`,
    `runaPadre`;";

$result = mysqli_query($dblink, $sql);

mysqli_query($dblink, "SET FOREIGN_KEY_CHECKS = 1");

if (!$result) {
    die("Error eliminando tablas: " . mysqli_error($dblink));
}
?>