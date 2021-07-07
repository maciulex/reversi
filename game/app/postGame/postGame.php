<?php
    session_start();
    include_once "../../../base.php";
    if (!isset($_SESSION['nickname']) || !isset($_SESSION['authCode'])) {
        session_destroy();
        header("Location: ../../index.php");
        exit();
    } else {
        @include_once "../../user/loggedCheck.php";
    }
    if (!isset($_GET['serverName'])) {
        echo "error 1";
        exit();
    }
    $connection = new mysqli($db_host, $db_user, $db_password, $db_name);
    $players;
    $board;
    $sql = "SELECT name, status, playersNicks, privacy, players, gameEnd, score, board FROM gamestictactoe WHERE BINARY name = BINARY ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> bind_param("s", $_GET['serverName']);
    $stmt -> execute();
    $stmt -> store_result();
    $rows = $stmt -> num_rows;
    $stmt -> bind_result($name, $status, $playersNicks, $privacy, $playersINT, $gameEnd,$score,$board);
    $stmt -> fetch();
    if ($rows == 1) {
        $players = explode(";", $playersNicks);
        echo $name.";".$status.";".$privacy.";".$playersINT.";".$players[0].";".$gameEnd.";".$score.";".$playersNicks;
    } else {
        echo "error 3 ".$_GET['serverName'];
        $stmt -> close();
        mysqli_close($connection);
        exit();
    }
    $stmt -> close();
    echo ";;;"; // great separator;
    $sql = "SELECT nickname, descryption, avatar, Sgames, SgamesWin, SgamesLose,SgamesAbound,SgamesEarlyEnd,SgamesDraw FROM users WHERE BINARY nickname = BINARY ?";
    $stmt = $connection -> prepare($sql);
    $data = array();
    foreach ($players as $key) {
        if ($key != "" && !empty($key)) {
            $me = "false";
            if ($key == $_SESSION['nickname']) {
                $me = "true";
            }
            $stmt -> bind_param("s", $key);
            $stmt -> execute();
            $stmt -> store_result();
            $stmt -> bind_result($nickname, $descryption, $avatar, $Sgames, $SgamesWin, $SgamesLose, $SgamesAbound, $SgamesEarlyEnd, $SgamesDraw);
            $stmt -> fetch();
            if ($avatar == "" || empty($avatar)) {
                $avatar = "false";
            }
            $data[] = $me.";".$nickname.";".$descryption.";".$avatar.";".$Sgames.";".$SgamesWin.";".$SgamesLose.";".$SgamesAbound.";".$SgamesEarlyEnd.";".$SgamesDraw;
        }
    }
    echo implode(";;", $data);
    $stmt -> close();
    echo ";;;".$board;
    mysqli_close($connection);
?>