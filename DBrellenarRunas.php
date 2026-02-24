<?php    
    require_once __DIR__.'/../../../data/config.php';

    $dblink = mysqli_connect("dbserver", "grupo".GRUPO,
            DB_pasword, "db_grupo".GRUPO);

    if (!$dblink) {
        die("Conexion fallida: " . mysqli_connect_error());
    }

    $urlRunas = "https://ddragon.leagueoflegends.com/cdn/16.3.1/data/es_ES/runesReforged.json";
    $jsoRunas = file_get_contents($urlRunas);
    $dataRunas = json_decode($jsoRunas, true);
    foreach($dataRunas as $runaPadre){
        echo "Insertando runa padre: ".$runaPadre['name']."<br>";
        $nombreRunaPadre = mysqli_real_escape_string($dblink, $runaPadre['name']);
        $imagenRune = "https://ddragon.leagueoflegends.com/cdn/img/".$runaPadre['icon'];
        $sql = "INSERT INTO final_runaPadre (nombre, imagen) VALUES ('$nombreRunaPadre', '$imagenRune') ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), imagen = VALUES(imagen)";
        $result = mysqli_query($dblink, $sql);
        if (!$result) {
            die("Error insertando runa padre: " . mysqli_error($dblink));
        }
        $runaPadre_id = mysqli_insert_id($dblink);
        foreach($runaPadre['slots'] as $i => $slot){
            foreach($slot['runes'] as $rune){
                echo "Insertando runa: ".$rune['name']."<br>";
                $nombreRune = mysqli_real_escape_string($dblink, $rune['name']);
                $descripcionRune = mysqli_real_escape_string($dblink, $rune['longDesc']);
                $imagenRune = "https://ddragon.leagueoflegends.com/cdn/img/".$rune['icon'];
                $sql = "INSERT INTO final_runa (nombre, descripcion, imagen, ranura, runaPadre_id)
                        VALUES ('$nombreRune', '$descripcionRune', '$imagenRune', '$i', '$runaPadre_id') 
                        ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), descripcion = VALUES(descripcion), imagen = VALUES(imagen), ranura = VALUES(ranura), runaPadre_id = VALUES(runaPadre_id)";
                $result = mysqli_query($dblink, $sql);
                if (!$result) {
                    die("Error insertando runa: " . mysqli_error($dblink));
                }
            }
        }
    }
    echo "        <form action=\"index.php\">
            <button type=\"submit\">volver</button>
        </form>";
?>
