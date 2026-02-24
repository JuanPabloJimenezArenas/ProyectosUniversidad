<?php
    require_once __DIR__.'/../../../data/config.php';

    $dblink = mysqli_connect("dbserver", "grupo".GRUPO,
            DB_pasword, "db_grupo".GRUPO);

    if (!$dblink) {
        die("Conexion fallida: " . mysqli_connect_error());
    }

    $sql = "CREATE TABLE IF NOT EXISTS final_especie(
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(30) NOT NULL
    )";
    $result = mysqli_query($dblink, $sql);
    if (!$result) {
        die("Error creadon tabla especie: " . mysqli_error($dblink));
    }


    $sql = "CREATE TABLE IF NOT EXISTS final_region (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(30) NOT NULL
    )";
    $result = mysqli_query($dblink, $sql);
    if (!$result) {
        die("Error creadon tabla region: " . mysqli_error($dblink));
    }

    $sql = "CREATE TABLE IF NOT EXISTS final_campeon (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(30) NOT NULL,
        nombre_US VARCHAR(30) NOT NULL,
        titulo VARCHAR(75),
        resumen TEXT,
        lore TEXT,
        imagen VARCHAR(255),
        regionOrigen_id INT(6) UNSIGNED,
        FOREIGN KEY (regionOrigen_id) REFERENCES final_region(id)
    )";
    $result = mysqli_query($dblink, $sql);
    if (!$result) {
        die("Error creadon tabla campeon: " . mysqli_error($dblink));
    }
    
    $sql = "CREATE TABLE IF NOT EXISTS final_habilidad (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(75) NOT NULL,
        tecla VARCHAR(2),
        descripcion TEXT,
        imagen VARCHAR(255),
        campeon_id INT(6) UNSIGNED,
        FOREIGN KEY (campeon_id) REFERENCES final_campeon(id)
    )";
    $result = mysqli_query($dblink, $sql);
    if (!$result) {
        die("Error creadon tabla habilidad: " . mysqli_error($dblink));
    }

    $sql = "CREATE TABLE IF NOT EXISTS final_campeon_especie(
        campeon_id INT(6) UNSIGNED,
        especie_id INT(6) UNSIGNED,
        PRIMARY KEY (campeon_id, especie_id),
        FOREIGN KEY (campeon_id) REFERENCES final_campeon(id),
        FOREIGN KEY (especie_id) REFERENCES final_especie(id)
    )";
    $result = mysqli_query($dblink, $sql);
    if (!$result) {
        die("Error creadon tabla campeon_especie: " . mysqli_error($dblink));
    }


    $sql = "CREATE TABLE IF NOT EXISTS final_campeon_region (
        campeon_id INT(6) UNSIGNED,
        region_id INT(6) UNSIGNED,
        PRIMARY KEY (campeon_id, region_id),
        FOREIGN KEY (campeon_id) REFERENCES final_campeon(id),
        FOREIGN KEY (region_id) REFERENCES final_region(id)
    )";
    $result = mysqli_query($dblink, $sql);
    if (!$result) {
        die("Error creadon tabla campeon_region: " . mysqli_error($dblink));
    }

    $sql = "CREATE TABLE IF NOT EXISTS final_runaPadre(
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(30) NOT NULL UNIQUE,
        imagen VARCHAR(255)
    )";
    $result = mysqli_query($dblink, $sql);
    if (!$result) {
        die("Error creando tabla runaPadre: " . mysqli_error($dblink));
    }

    $sql = "CREATE TABLE IF NOT EXISTS final_runa(
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(30) NOT NULL,
        descripcion TEXT,
        imagen VARCHAR(255),
        ranura INT(1),
        runaPadre_id INT(6) UNSIGNED,
        FOREIGN KEY (runaPadre_id) REFERENCES final_runaPadre(id)
    )";
    $result = mysqli_query($dblink, $sql);
    if (!$result) {
        die("Error creando tabla runa: " . mysqli_error($dblink));
    }

    $sql = "CREATE TABLE IF NOT EXISTS final_objeto(
        id INT(10) UNSIGNED  PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        imagen VARCHAR(255),
        estadisticas TEXT,
        precio_base INT(10),
        precio_total INT(10),
        precio_venta INT(10)
    )";
    $result = mysqli_query($dblink, $sql);
    if (!$result) {
        die("Error creando tabla objeto: " . mysqli_error($dblink));
    }

    $sql = "CREATE TABLE IF NOT EXISTS final_objeto_recetas(
        objeto_id INT(10) UNSIGNED,
        componente_id INT(10) UNSIGNED,
        cantidad INT DEFAULT 1,
        PRIMARY KEY (objeto_id, componente_id),
        FOREIGN KEY (objeto_id) REFERENCES final_objeto(id),
        FOREIGN KEY (componente_id) REFERENCES final_objeto(id)
    )";
    $result = mysqli_query($dblink, $sql);
    if (!$result) {
        die("Error creando tabla objeto_recetas: " . mysqli_error($dblink));
    }
    echo "        <form action=\"index.php\">
            <button type=\"submit\">volver</button>
        </form>";
?>

