<?php
    require_once __DIR__.'/../../../data/config.php';

    $dblink = mysqli_connect("dbserver", "grupo".GRUPO,
            DB_pasword, "db_grupo".GRUPO);

    if (!$dblink) {
        die("Conexion fallida: " . mysqli_connect_error());
    }

    $urlItiems = "https://ddragon.leagueoflegends.com/cdn/16.3.1/data/es_ES/item.json";
    $jsoITems = file_get_contents($urlItiems);
    $dataItems = json_decode($jsoITems, true);
    $items = $dataItems['data'];

    foreach($items as $id => $item){
        if (isset($item["maps"]["11"]) && $item["maps"]["11"] == true && isset($item['gold']['purchasable']) && $item['gold']['purchasable'] == true) {
            echo "Insertando item: ".$item['name']."<br>";
            $id_nombre = mysqli_real_escape_string($dblink, $id);
            $nombre = mysqli_real_escape_string($dblink, $item['name']);
            $descripcion = mysqli_real_escape_string($dblink, $item['description']);
            $imagen = "https://ddragon.leagueoflegends.com/cdn/16.3.1/img/item/".$item['image']['full'];
            $precio_base = $item['gold']['base'];
            $precio_total = $item['gold']['total'];
            $precio_venta = $item['gold']['sell'];
            $sql = "INSERT INTO final_objeto (id, nombre, descripcion, imagen, precio_base, precio_total, precio_venta) VALUES ('$id_nombre', '$nombre', '$descripcion', '$imagen', '$precio_base', '$precio_total', '$precio_venta')";
            $result = mysqli_query($dblink, $sql);
            if (!$result) {
                die("Error insertando objeto: " . mysqli_error($dblink));
            }
        }
    }

    foreach($items as $id_componenete => $item){
        if (isset($item["maps"]["11"]) && $item["maps"]["11"] == true && isset($item['into'])) {
            foreach ($item['into'] as  $id) {
                echo "Insertando receta: componene ->".$item["name"].": ".$id_componenete." -> receta: ".$id."<br>";
                $componente_id = mysqli_real_escape_string($dblink, $id_componenete);
                $item_id = mysqli_real_escape_string($dblink, $id);
                $sql = "INSERT INTO final_objeto_recetas (objeto_id, componente_id)
                        SELECT '$item_id', '$componente_id'
                        WHERE EXISTS (SELECT 1 FROM final_objeto WHERE id = '$item_id')
                        AND EXISTS (SELECT 1 FROM final_objeto WHERE id = '$componente_id')";
                $result = mysqli_query($dblink, $sql);
                if (!$result) {
                    die("Error insertando receta: " . mysqli_error($dblink));
                }
            }
        }
    }
?>