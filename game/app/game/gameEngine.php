<?php
    session_start();
    if (!isset($_SESSION['nickname']) || !isset($_SESSION['authCode'])) {
        session_destroy();
        header("Location: ../../index.php");
        exit();
    } else {
        @include_once "../../user/loggedCheck.php";
    }
    if (!isset($_SESSION['serverName']) || !isset($_SESSION['serverName']) || !isset($_GET['action'])) {
        echo "error 1";
        exit();
    }
    $action = intval($_GET['action']);
    include_once "../../../base.php";
    $connection = new mysqli($db_host, $db_user, $db_password, $db_name);
    if ($connection -> connect_errno > 0) {
        echo "error 2";
        exit();
    }
    switch ($action) {
        case 0:
            //getGameData
            $sql = "SELECT name, playersNicks, whosTour, timeout, lastAction, status, board FROM reversi WHERE name = ?";
            $stmt = $connection -> prepare($sql);
            $stmt -> bind_param("s", $_SESSION['serverName']);
            $stmt -> execute();
            $stmt -> store_result();
            $stmt -> bind_result($name, $playersNicks, $whosTour, $timeout, $lastAction, $status, $board);
            $stmt -> fetch();
            echo $name.";;;".$playersNicks.";;;".$whosTour.";;;".$board.";;;".$timeout.";;;".$lastAction.";;;".time().";;;".$status;
            $stmt -> close();
        break;
        case 1:
            //shoting
            if (!isset($_GET["cord"]) || intval($_GET["cord"]) < 0 || intval($_GET["cord"]) > 63) {
                echo "error 3";
                mysqli_close($connection);
                exit();
            } 
            
        break;
        case 2:
            //early end >:
            $lastAction; $playersNicks; $status;
            $sql = "SELECT lastAction, playersNicks, status FROM reversi WHERE name = ?";
            $stmt = $connection -> prepare($sql);
            $stmt -> bind_param("s", $_SESSION["serverName"]);
            $stmt -> execute();
            $stmt -> store_result();
            $rows = $stmt->num_rows;
            $stmt -> bind_result($lastAction, $playersNicks, $status);
            $stmt -> fetch();
            $stmt -> close();
            if (intval($lastAction)+300 < time() && $status == "2") {
                if ($rows == 1) {
                    $playersNicks = explode(";", $playersNicks);
                    foreach ($playersNicks as $key) {
                        if ($key == $_SESSION["nickname"]) {
                            $sql = "UPDATE users SET SgamesAbound = SgamesAbound + 1 WHERE nickname = ?";
                            $stmt = $connection -> prepare($sql);
                            $stmt -> bind_param("s", $key);
                            $stmt -> execute();
                            $stmt -> close();
                        } else {
                            $sql = "UPDATE users SET  SgamesEarlyEnd = SgamesEarlyEnd + 1 WHERE nickname = ?";
                            $stmt = $connection -> prepare($sql);
                            $stmt -> bind_param("s", $key);
                            $stmt -> execute();
                            $stmt -> close();
                        }
                    }
                    $sql = 'UPDATE reversi SET status = 3, gameEnd = CONCAT("Po 5 min bezczynności gra zakończona za życzenie gracza: ", ?) WHERE name = ?';
                    $stmt = $connection -> prepare($sql);
                    $stmt -> bind_param("ss", $_SESSION['nickname'], $_SESSION['serverName']);
                    $stmt -> execute();
                    $stmt -> close();
                    $stats = array(0,1);
                    $sql = "UPDATE users SET inGame = 0, SgamesAbound = SgamesAbound + ?, SgamesEarlyEnd = SgamesEarlyEnd + ? WHERE nickname = ?";
                    $stmt = $connection -> prepare($sql);
                    foreach ($playersNicks as $key) {
                        if ($key == $_SESSION["nickname"]) {
                            $stmt -> bind_param("iis", $stats[0], $stats[1], $key);
                            $stmt -> execute();
                        } else {
                            $stmt -> bind_param("iis", $stats[1], $stats[0], $key);
                            $stmt -> execute();
                        }
                    }
                    $stmt -> close();
                }
            }
        break;
    }
    mysqli_close($connection);
?>