<?php
    require_once __DIR__.'/../../../data/config.php';

    $dblink = mysqli_connect("dbserver", "grupo".GRUPO,
            DB_pasword, "db_grupo".GRUPO);

    if (!$dblink) {
        die("Conexion fallida: " . mysqli_connect_error());
    }


    $urlRegiones = "https://wiki.leagueoflegends.com/en-us/api.php?action=query&list=categorymembers&cmtitle=Category:Lore%20by%20region&cmtype=subcat|page&cmlimit=500&format=json";
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

    if ($httpCode !== 200) {
        die("Error HTTP: $httpCode");
    }       
    
    $dataRegiones = json_decode($jsoRegiones, true);
    $regiones = $dataRegiones['query']['categorymembers'];

    foreach ($regiones as $region) {
        echo "Insertando region: ".$region['title']."<br />";
        $nombreRegion = mysqli_real_escape_string($dblink, explode("Category:Lore of ", $region['title'])[1]);
        $sql = "INSERT INTO final_region (nombre) VALUES ('$nombreRegion')";
        $result = mysqli_query($dblink, $sql);
        if (!$result) {
            die("Error insertando region ".$nombreRegion.": " . mysqli_error($dblink));
        }
    }
    echo "        <form action=\"index.php\">
            <button type=\"submit\">volver</button>
        </form>";
?>