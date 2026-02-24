<?php

    require_once __DIR__.'/../../../data/config.php';

    $dblink = mysqli_connect("dbserver", "grupo".GRUPO,
            DB_pasword, "db_grupo".GRUPO);

    if (!$dblink) {
        die("Conexion fallida: " . mysqli_connect_error());
    }

    $slq = "SELECT id,nombre,nombre_US FROM final_campeon";
    $result = mysqli_query($dblink, $slq);
    if (!$result) {
        die("Error fetching data: " . mysqli_error($dblink));
    }
    $campeones = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach ($campeones as $campeon) {
    //$campeon = $campeones[164];
        $titulo = urlencode("Universe:" . $campeon["nombre_US"]);

        $urlRegiones = "https://wiki.leagueoflegends.com/en-us/api.php?action=query&format=json&prop=revisions&titles=".$titulo."&rvslots=main&rvprop=content";
        $ch = curl_init($urlRegiones);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "User-Agent: AcademicProject/1.0",
                "Accept: application/json"
            ]
        ]);

        $jsoRegiones = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($jsoRegiones, true);
        $page = reset($data['query']['pages']);
        $wikitext = $data['query']['pages'][$page["pageid"]]['revisions'][0]['slots']['main']['*'] ?? '';


        $regiones = [];
        $especies = [];
        echo "Procesando campeon: ".$campeon["nombre"]."<br />";
        echo "Wikitext length: ".strlen($wikitext)."<br />";
        if (preg_match('/\|\s*region\s*=\s*(.*?)(\n\|)/is', $wikitext, $matches)) {
            $bloque = $matches[1];
            preg_match_all('/\{\{fi\|([^}]+)\}\}/i', $bloque, $fi_matches);
            $regiones = array_merge($regiones, $fi_matches[1]);
            preg_match_all('/\[\[([^|\]]+)/i', $bloque, $link_matches);
            $regiones = array_merge($regiones, $link_matches[1]);
            $regiones = array_unique(array_map('trim', $regiones));
        }
        foreach ($regiones as $region) {
            echo "Insertando relacion entre campeon: ".$campeon["nombre"]." y region: ".$region."<br />";
            $region_safe = mysqli_real_escape_string($dblink, $region);
            $sqlIdRegion = "SELECT id FROM final_region WHERE nombre = '$region_safe'";
            $resultRegion = mysqli_query($dblink, $sqlIdRegion);
            if ($row = mysqli_fetch_assoc($resultRegion)) {
                $region_id = $row['id'];
                $sqlInsert = "INSERT INTO final_campeon_region (campeon_id, region_id) VALUES ('".$campeon["id"]."', '".$region_id."')";
                if (!mysqli_query($dblink, $sqlInsert)) {
                    echo "Error inserting relation for ".$campeon["nombre"]." and ".$region.": " . mysqli_error($dblink);
                }
            } else {
                echo "Region not found: ".$region;
            }
        }
        if (preg_match('/\|\s*species\s*=\s*(.*?)(\n\|)/is', $wikitext, $matches)) {
            $bloque = $matches[1];
            preg_match_all('/\{\{fi\|([^}]+)\}\}/i', $bloque, $fi_matches);
            $especies = array_merge($especies, $fi_matches[1]);
            preg_match_all('/\[\[([^|\]]+)/i', $bloque, $link_matches);
            $especies = array_merge($especies, $link_matches[1]);
            $especies = array_unique(array_map('trim', $especies));
        }
        foreach($especies as $especie){
            $especie = str_replace("Universe:", "", $especie);
            $especie_safe = mysqli_real_escape_string($dblink, $especie);
            $sqlIdEspecie = "SELECT id FROM final_especie WHERE nombre = '$especie_safe'";
            $resultEspecie = mysqli_query($dblink, $sqlIdEspecie);
            if ($row = mysqli_fetch_assoc($resultEspecie)) {
                $especie_id = $row['id'];
                $sqlInsert = "INSERT INTO final_campeon_especie (campeon_id, especie_id) VALUES ('".$campeon["id"]."', '".$especie_id."')";
                if (!mysqli_query($dblink, $sqlInsert)) {
                    echo "Error inserting relation for ".$campeon["nombre_US"]." and ".$especie.": " . mysqli_error($dblink);
                }
            } else {
                echo "Especie not found: ".$especie;
            }
            echo "Insertando relacion entre campeon: ".$campeon["nombre_US"]." y especie: ".$especie."<br />";
        }
        usleep(100);
    }
?>