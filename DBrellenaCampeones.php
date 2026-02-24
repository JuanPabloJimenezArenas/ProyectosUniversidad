<?php    
    require_once __DIR__.'/../../../data/config.php';

    $dblink = mysqli_connect("dbserver", "grupo".GRUPO,
            DB_pasword, "db_grupo".GRUPO);

    if (!$dblink) {
        die("Conexion fallida: " . mysqli_connect_error());
    }

    $urlCampeones = "https://ddragon.leagueoflegends.com/cdn/16.3.1/data/es_ES/championFull.json";
    $urlCampeones_US = "https://ddragon.leagueoflegends.com/cdn/16.3.1/data/en_US/championFull.json";
    $jsoCampeones = file_get_contents($urlCampeones);
    $jsoCampeones_US = file_get_contents($urlCampeones_US);
    $dataCampeones = json_decode($jsoCampeones, true);
    $dataCampeones_US = json_decode($jsoCampeones_US, true);
    $champs = $dataCampeones['data'];
    $champs_US = $dataCampeones_US['data'];
    foreach($champs as $i => $champ){
        $champ_US = $champs_US[$i];
        echo "Insertando campeon: ".$champ['name']."  ".$champ_US['name']."<br>";
        $nombre = mysqli_real_escape_string($dblink, $champ['name']);
        $nombre_US = mysqli_real_escape_string($dblink, $champ_US['name']);
        $titulo = mysqli_real_escape_string($dblink, $champ['title']);
        $resumen = mysqli_real_escape_string($dblink, $champ['blurb']);
        $lore = mysqli_real_escape_string($dblink, $champ['lore']);
        $imagen = "https://ddragon.leagueoflegends.com/cdn/16.3.1/img/champion/".$champ['image']['full'];

        $sql = "INSERT INTO final_campeon (nombre, nombre_US, titulo, resumen, lore, imagen)
                VALUES ('$nombre','$nombre_US', '$titulo','$resumen','$lore','$imagen')";

        $result = mysqli_query($dblink, $sql);
        if (!$result) {
            die("Error insertando campeon: " . mysqli_error($dblink));
        }

        $campeon_id = mysqli_insert_id($dblink);

        $teclas = ['Q', 'W', 'E', 'R'];

        foreach ($champ['spells'] as $i => $spell) {
            echo "Insertando habilidad: ".$spell['name']."<br>";
            $nombreSpell = mysqli_real_escape_string($dblink, $spell['name']);
            $descripcion = mysqli_real_escape_string($dblink, $spell['description']);
            $imagenSpell = "https://ddragon.leagueoflegends.com/cdn/16.3.1/img/spell/".$spell['image']['full'];
            $tecla = mysqli_real_escape_string($dblink, $teclas[$i]);

            $sql = "INSERT INTO final_habilidad (nombre, tecla, descripcion, imagen, campeon_id)
                    VALUES ('$nombreSpell', '$tecla', '$descripcion', '$imagenSpell', $campeon_id)";
            $result = mysqli_query($dblink, $sql);
            if (!$result) {
                die("Error insertando habilidad: ".$nombreSpell.": " . mysqli_error($dblink));
            }
        }   

        $nombrePasiva = mysqli_real_escape_string($dblink, $champ['passive']['name']);
        $descripcionPasiva = mysqli_real_escape_string($dblink, $champ['passive']['description']);
        $imagenPasiva = "https://ddragon.leagueoflegends.com/cdn/16.3.1/img/passive/".$champ['passive']['image']['full'];
        $sql = "INSERT INTO final_habilidad (nombre, tecla, descripcion, imagen, campeon_id)
                VALUES ('$nombrePasiva', 'P', '$descripcionPasiva', '$imagenPasiva', $campeon_id)";
        $result = mysqli_query($dblink, $sql);
        if (!$result) {
            die("Error insertando pasiva de ".$nombrePasiva.": " . mysqli_error($dblink));
        }
    }
    echo "        <form action=\"index.php\">
            <button type=\"submit\">volver</button>
        </form>";
?>
