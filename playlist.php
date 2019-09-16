<?php
    
    require_once('scripts/database.php');
    require_once('scripts/helpfuncties.php');

    //query voor mijn eigen playlists gaan maken
    $sqlMijnPlayList = "SELECT playlistid, titel from savedplaylist s INNER JOIN playlist p ON s.playlistid = p.idplaylist";

    //alle info over de playlist zelf, een query met 1 resultaat dus!
    $sqlInfoplaylist = "SELECT titel, omschrijving, afbeelding, createdby, (SELECT sum( duur)
    FROM song s INNER JOIN songsopplaylist sp ON s.idsongs = sp.idsong
    WHERE idplaylist = ?) as duur,(SELECT count( duur)
    FROM song s INNER JOIN songsopplaylist sp ON s.idsongs = sp.idsong
    WHERE idplaylist = ?) as aantal
    FROM playlist
    WHERE idplaylist = ?";

    // query om de juiste songs van de gekozen playlist te tonen
    $sqlSongsplaylist = "SELECT title, duur, naam, cdtitel, sp.toegevoegd, s.idsongs FROM songsopplaylist sp 
    INNER JOIN song s ON sp.idsong = s.idsongs 
    INNER JOIN artiest a ON s.artistid = a.idartiest 
    INNER JOIN songopcd soc ON s.idsongs = soc.songid 
    INNER JOIN cd cd ON soc.cdid = cd.idcd
    WHERE idplaylist = ?";

    //query van mijn playlist uitvoeren om in NAV te plaatsen
    if(!$resNAVMijnplaylists = $mysqli->query($sqlMijnPlayList)){
        echo "Oeps, een query foutje op DB voor opzoeken eigen playlist";
        print("<p>Error: " . $mysqli->error ."</p>");
        exit();
    }

    //verwerking voor info van gekozen playlist
    if(isset($_GET['idplaylist'])){
        $gekozenID = $_GET['idplaylist'];
    }
    else{
        $gekozenID = -1;
    }

    $stmtInfo = $mysqli->prepare($sqlInfoplaylist);
    
    //parameters koppelen
    $stmtInfo->bind_param("iii", $parID1, $parID2, $parID3);
    $parID1 = $gekozenID;
    $parID2 = $gekozenID;
    $parID3 = $gekozenID;

    //query uitvoeren
    $stmtInfo->execute();

    //voer de query uit en haal het ENIGE resultaat op en stop het in resultInfo
    $resultInfo = $stmtInfo->get_result();

    //we hebben geen lus nodig, het is voldoende de eerste rij te vragen
    $rowinfoplaylist = $resultInfo->fetch_assoc();

    $stmtInfo->close();


    // Verwerking van SONGS op de gekozen playlist
    $stmtSongs = $mysqli->prepare($sqlSongsplaylist);
    $stmtSongs->bind_param("i", $parID1);
    $stmtSongs->execute();
    $resultSongs = $stmtSongs->get_result();

    // Alle songs zitten nu in resultSongs (versch. rijen), straks lus om uit te lezen


?>

<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,400" rel="stylesheet">
    <!-- Local Stylesheet -->
    <link rel="stylesheet" href="style/screen.css">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
        integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>

<body class="container-fluid h-100">
    <div id="container" class="row h-100">
        <aside class="col-2 h-100">
            <nav>
                <ul class="list-unstyled">
                    <li><a href="#">Browse</a></li>
                    <li><a href="#">Radio</a></li>
                </ul>
            </nav>
            <nav>
                <h1>your library</h1>
                <ul class="list-unstyled">
                    <li><a href="#">Your Daily Mix</a></li>
                    <li><a href="#">Recent Played</a></li>
                    <li><a href="song.php">Songs</a></li>
                    <li><a href="#">Albums</a></li>
                    <li><a href="#">Artists</a></li>
                    <li><a href="#">Stations</a></li>
                </ul>
            </nav>
            <nav>
                <h1>playlists</h1>
                <ul class="list-unstyled">
                    
                    <?php

                        //verwerking voor info van gekozen playlist
                        if(isset($_GET['idplaylist'])) {
                            $gekozenID = $_GET['idplaylist'];
                        }
                        else{
                            $gekozenID = -1;
                        }

                        //ophalen van het resultaat van de query
                        //doorlopen van het resultaat zolang er rijen zijn
                        while ($row = $resNAVMijnplaylists->fetch_assoc()) {
                            //opvullen tijdelijke var
                            $tempId = $row['playlistid'];
                            $tempTitel = $row['titel'];

                            if($tempId==$gekozenID){
                                //gebruiken van var om rij van LI te maken
                                print('<li class="active"><a href="playlist.php?idplaylist=' . $tempId . '">' . $tempTitel .'</a></li>');

                            }
                            else {
                                //gebruiken van var om rij van LI te maken
                                print('<li><a href="playlist.php?idplaylist=' . $tempId . '">' . $tempTitel .'</a></li>');
                            }
                        }
                    ?>

                </ul>
            </nav>
        </aside>
        <main class="col-10 h-100">
            <header class="row">
                <div class="col-6">
                    <i class="fas fa-chevron-left"></i>
                    <i class="fas fa-chevron-right"></i>
                    <form>
                        <input type="text" name="Search" id="Search">
                    </form>
                </div>
                <div class="col-6 text-right">
                    <img src="images/person.png" alt="Account">
                    Jens De Muynck
                    <a href="#"><i class="fas fa-chevron-down"></i></a>
                </div>
            </header>
            <section class="row" id="content">
                <header class="col-12">
                    <div class="row">
                        <div class="col-2" id="content-cover">
                            <img src="images/playlist/<?php print($rowinfoplaylist['afbeelding']); ?>" alt="Album Cover" class="img-fluid">
                        </div>
                        <div class="col-10" id="content-info">
                            <div class="type">type</div>
                            <h1><?php print($rowinfoplaylist['titel']); ?></h1>
                            <p><?php print($rowinfoplaylist['omschrijving']); ?></p>
                            <p>Created by <span class="author"><?php print($rowinfoplaylist['createdby']); ?></span> · <?php print($rowinfoplaylist['aantal']); ?>, <?php print($rowinfoplaylist['duur']); ?></p>
                        </div>
                        <div class="col-6" id="content-actions">
                            <a href="#" class="btn solid">Play</a>
                            <a href="#" class="btn">Following</a>
                            <a href="#" class="btn more"><i class="fas fa-ellipsis-h"></i></a>
                        </div>
                        <div class="col-6" id="content-followers">X aantal followers</div>
                    </div>
                </header>
                <div class="col-12 scrolledsmall">
                    <div class="row">
                        <div class="col-1"><img class="img-fluid" src="images/placeholder.png" alt=""></div>
                        <div class="col-6">
                            <h1>Yo momma <i class="fab fa-accessible-icon"></i></h1>
                        </div>
                        <div class="col-5 text-right">
                            <a href="#" class="btn solid">Play</a>
                            <a href="#" class="btn">Following</a>
                            <a href="#" class="btn more"><i class="fas fa-ellipsis-h"></i></a>
                        </div>
                    </div>
                </div>
                <section class="col-12 tabelview" id="bevat">
                    <div class="row">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>title</th>
                                    <th>artist</th>
                                    <th>album</th>
                                    <th><i class="far fa-calendar"></i></th>
                                    <th></th>
                                    <th><i class="far fa-clock"></i></th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                    while($row = $resultSongs->fetch_assoc()){

                                        $tempTitel = $row['title'];
                                        $tempName = $row['naam'];
                                        $tempCDTitel = $row['cdtitel'];
                                        $tempDate = $row['toegevoegd'];
                                        $tempDuur = $row['duur'];
                                        $tempSongID = $row['idsongs'];

                                        print('<tr>');
                                        print('<tr><td class="play_status"><i class="fas fa-volume-up"></i> <i class="far fa-play-circle"></i><i class="far fa-pause-circle"></i></td>');
                                        
                                        // De link voegt een liedje toe aan saved songs
                                        print('<td><a href="songs_add.php?idsong='. $tempSongID .'"><i class="fas fa-plus"></i></a></td>');
                                        print('<td>'. $tempTitel .'</td>');
                                        print('<td>'. $tempName .'</td>');
                                        print('<td>'. $tempCDTitel .'</td>');
                                        print('<td>'. $tempDate .'</td>');
                                        print('<td><i class="fas fa-ellipsis-h"></i></td>');
                                        print('<td>' .sec_naar_tijd($tempDuur). '</td>');
                                        print('</tr>');


                                    }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </section>
            </section>
        </main>
    </div>

    <footer class="row fixed-bottom schaduw">
        <section class="col-3">
            <div class="row" id="nav_playing">
                <div class="col-4"><img src="images/placeholder.png" alt="now playing" class="img-fluid"></div>
                <div class="col-8 m-auto">
                    <section class="infoplaying">
                        <div class="songtitle">title</div>
                        <div class="artist">artist</div>
                    </section>
                </div>
            </div>
        </section>
        <section class="col-6 m-auto" id="nav_ctrl">
            <div class="row">
                <div class="col-1 text-center">x:xx</div>
                <div class="col-10 m-auto">
                    <div class="playbar">
                        <div class="currentposbar">

                        </div>
                        <div class="currentpos">

                        </div>
                    </div>
                </div>
                <div class="col-1 text-center">x:xx</div>
            </div>
        </section>
        <section class="col-3 m-auto" id="nav_remote">
            <div class="row">
                <div class="col-4"></div>
                <div class="col-5">
                    <div class="playbar">
                        <div class="currentposbar">

                        </div>
                        <div class="currentpos">

                        </div>
                    </div>
                </div>
                <div class="col-3"></div>
            </div>
        </section>
    </footer>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="js/scroll.js"></script>
</body>

</html>