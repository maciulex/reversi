<?php
    include_once "../base.php";
    $mainIndexPath = "../index.php";
    session_start();

    if (!isset($_SESSION['nickname']) || !isset($_SESSION['authCode'])) {
        session_destroy();
        header("Location: ../index.php");
        exit();
    } else {
        @include_once "../user/loggedCheck.php";
    }
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <title>Reversi!</title>
        <meta charset="utf-8">
        <link href="../styles/gameList/style.css" rel="stylesheet">
        <script src="app/mainIndexApp.js"></script>
        <script>var motiveAccess = <?php  echo ((isset($_COOKIE['motive'])) ? $_COOKIE['motive'] : 0); ?>;</script>
        <script src="../mainApp.js"></script>
    </head>
    <body>
        <header>
            <a href="../user/indexManagment/logout.php"><button>Wyloguj</button></a>
            <?php
                $connection = new mysqli($db_host, $db_user, $db_password, $db_name);
                $sql = "SELECT inGame FROM users WHERE BINARY nickname = BINARY ?";
                $stmt = $connection -> prepare($sql);
                $stmt -> bind_param("s", $_SESSION['nickname']);
                $stmt -> execute();
                $stmt -> store_result();
                $stmt -> bind_result($inGame);
                $stmt -> fetch();
                if (intval($inGame) != 0) {
                    $sql = "SELECT status, players FROM reversi WHERE BINARY name = BINARY ?";
                    $stmt2 = $connection -> prepare($sql);
                    $stmt2 -> bind_param("s", $_SESSION['serverName']);
                    $stmt2 -> execute();
                    $stmt2 -> store_result();
                    $stmt2 -> bind_result($status, $players);
                    $stmt2 -> fetch();
                    switch (intval($status)) {
                        case 1:
                            $status = "W poczekalni";
                        break;
                        case 2:
                            $status = "W grzę";
                        break;
                        case 4:
                            $status = "Poczekalnia rewanżu";
                        break;
                    }
                    echo '
                    <section class="gameReturnBlock">
                        <div>Nazwa servera: </div>
                        <div>'.$_SESSION["serverName"].'</div>
                        <div> | </div>
                        <div>Graczy: '.$players.'/2</div>
                        <div> | </div>
                        <div>Status: '.$status.'</div>
                        <div> | </div>
                        <button onclick=\'location="../game/gameQueue.php"\'>Powróć</button>
                    </section>
                    ';
                    $stmt2 -> close();
                }
                $stmt -> close();
                mysqli_close($connection);
            ?>
            <a href="profil.php" class="right"><button>Profil</button></a>
            <a href="gameCreate.php" class="right" style="width:75px"><button>Stwórz grę</button></a>
        </header>    
        <section>
            <aside>
                <br><br><br>
                <div class='noSelectText'>Wyszukiwarka</div>
                <br>
                <input type="text" placeholder="Nazwa gry" name="gameName">
                <br><br>
                <div class='noSelectText'>Dostępność:</div>
                <br>
                <select name="gamePrivacy">
                    <option value="1">Wszystkie</option>
                    <option value="2" selected>Publiczna</option>
                    <option value="3">Prywatna</option>
                </select>
                <br><br>
                <div class='noSelectText'>Status:</div>
                <br>
                <select name="gameStatus">
                    <option value="1">Wszystkie</option>
                    <option value="2" selected>Nie rozpoczęte</option>
                    <option value="3">Rozpoczęte</option>
                    <option value="4">Zakończone</option>
                </select>
                <br><br>
                <div class='noSelectText'>Zapełnienie:</div>
                <br>
                <select name="gameFull">
                    <option value="1">Wszystkie</option>
                    <option value="2">Pełne</option>
                    <option value="3" selected>Nie pełne</option>
                    <option value="4">1/2</option>
                    <option value="5">Puste</option>
                </select>
                <br><br>
                <div class='noSelectText'><button onclick="loadGames()">Szukaj</button><button onclick="loadGames()">Odśwież</button></div>
            </aside>
            <main>
                <div class="gameList">

                </div>
            </main>
        <section>
        <div class="changeMotive" onclick="changeMotive()"> 
            <img src="../photos/ico/sun-solid.svg">
        </div>
        <script>
            let error = <?php echo ((isset($_SESSION['error'])) ? '"'.$_SESSION['error'].'"' : "undefined");?>;
            if (error != undefined) {
                alert(error);
            }
            loadGames();
            setMotive();
        </script>
    </body>
</html>
<?php
    if (isset($_SESSION['error'])) {
        unset($_SESSION['error']);
    }
?>