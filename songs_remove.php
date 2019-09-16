<?php

    require_once('scripts/database.php');

    if(isset($_GET['idsong'])){
        // ophalen waarde idsong
        $id = $_GET['idsong'];

        $sql = "DELETE FROM savedsongs WHERE songid = ?";

        $stmt = $mysqli->prepare($sql);

        // parameter meekomen
        $stmt->bind_param("i", $id);

        // query uitvoeren
        $stmt->execute();

        $stmt->close();
    }

    header("location:song.php");

?>