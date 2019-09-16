<?php

    require_once('scripts/database.php');

    if(isset($_GET['idsong'])){
        // ophalen waarde idsong
        $id = $_GET['idsong'];

        $sql = "INSERT INTO savedsongs(songid) VALUE (?)";

        $stmt = $mysqli->prepare($sql);

        // parameter meekomen
        $stmt->bind_param("i", $id);

        // query uitvoeren
        $stmt->execute();

        $stmt->close();
    }

    header("location:song.php");

?>