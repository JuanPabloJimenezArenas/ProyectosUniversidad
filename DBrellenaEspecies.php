<?php

    require_once __DIR__.'/../../../data/config.php';

    $dblink = mysqli_connect("dbserver", "grupo".GRUPO,
            DB_pasword, "db_grupo".GRUPO);

    if (!$dblink) {
        die("Conexion fallida: " . mysqli_connect_error());
    }

    $urlEspecies = "https://wiki.leagueoflegends.com/en-us/api.php?action=query&list=categorymembers&cmtitle=Category:Sapient_species&cmtype=page&cmlimit=500&format=json";
    $ch = curl_init($urlEspecies);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "User-Agent: AcademicProject/1.0",
            "Accept: application/json"
        ]
    ]);
    $jsoEspecies = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
        die("Error HTTP: $httpCode");
    }
    $dataEspecies = json_decode($jsoEspecies, true);
    $especies = $dataEspecies['query']['categorymembers'];

    foreach ($especies as $especie) {
        $nombreEspecie = explode("Universe:", $especie['title']);
        if(count($nombreEspecie) < 2 or "Minion (Species)" == $nombreEspecie[1]){
            echo "Especie ".$especie['title']." no tiene formato esperado, se omite.<br />";
            continue;
        }
        else{
            $nombreEspecie = mysqli_real_escape_string($dblink, explode("Universe:", $especie['title'])[1]);
            echo "Insertando especie: ".$nombreEspecie."<br />";
            $sql = "INSERT INTO final_especie (nombre) VALUES ('$nombreEspecie')";
            $result = mysqli_query($dblink, $sql);
            if (!$result) {
                die("Error insertando especie ".$nombreEspecie.": " . mysqli_error($dblink));
            }
        }
    }
    echo "        <form action=\"index.php\">
            <button type=\"submit\">volver</button>
        </form>";
?>