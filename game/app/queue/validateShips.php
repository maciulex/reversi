<?php
    session_start();
    if (!isset($_SESSION['nickname']) || !isset($_SESSION['authCode'])) {
        session_destroy();
        header("Location: ../../index.php");
        exit();
    } else {
        @include_once "../../user/loggedCheck.php";
    }
    if (!isset($_SESSION['serverName']) || !isset($_GET['data']) || !isset($_SESSION['serverName'])) {
        echo "error 1";
        exit();
    }
    $shipsValidationI = array(0,0,0,0,0,0);
    $shipsValidationO = array(0,0,0,0,0,0);
    include_once "../../../base.php";
    $connection = new mysqli($db_host, $db_user, $db_password, $db_name);
    if ($connection -> connect_errno > 0) {
        echo "error";
        exit();
    }
    $gameShips;$playersNicks;$status;
    $sql = "SELECT gameShips, playersNicks, status FROM games WHERE name = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> bind_param("s", $_SESSION['serverName']);
    $stmt -> execute();
    $stmt -> store_result();
    $rows = $stmt -> num_rows;
    if ($rows != 1){ 
        echo "error 2";
        $stmt -> close();
        mysqli_close($connection);
        exit();
    }
    $stmt -> bind_result($gameShips,$playersNicks, $status);
    $stmt -> fetch();
    $stmt -> close();
    if ($status != "2") {
        echo "error 2.5";
        mysqli_close($connection);
        exit();
    }
    $gameShips = explode(";;", $gameShips);
    foreach ($gameShips as $key) {
        $localKey = explode(";", $key);
        $shipsValidationO[intval($localKey[1])] = intval($localKey[0]);
    }
    unset($gameShips);
    $ships = explode(";;;", $_GET['data']);
    foreach ($ships as $key) {
        $localKey = explode(";;",$key);
        $shipsValidationI[count($localKey)] += 1;
    }
    $validation = true;
    for ($i = 0; $i < 5; $i++) {
        if ($shipsValidationI[$i] != $shipsValidationO[$i]) {
            $validation = false;
        }
    }
    foreach ($ships as $key) {
        $localKey = explode(";;",$key);
        $dir = 0;
        for ($i = 0; $i < count($localKey)-1; $i++) {
            if ($localKey[$i][0] != $localKey[$i+1][0]-1) {
                if ($localKey[$i][2] != $localKey[$i+1][2]-1) {
                    $validation = false;
                }
            }
        }
    }
    if ($validation) {
        echo "Walidacja udana!";
        $playersNicks = explode(";",$playersNicks);
        $me = -1;
        for ($i = 0; $i < 2; $i++) {
            if ($playersNicks[$i] == $_SESSION['nickname']) {
                switch ($i){ 
                    case 0:
                        $me = "shipsP1";
                    break;
                    case 1:
                        $me = "shipsP2";
                    break;
                }
                break;
            }
        }
        if ($me == -1) {
            echo "error 3";
            mysqli_close($connection);
            exit();
        }
        $plain = "0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0;0";
        $plain = explode(";", $plain);
        foreach ($ships as $key) {
            $localKey = explode(";;", $key);
            foreach ($localKey as $ubu) {
                $ubu = explode(";",$ubu);
                $location = intval($ubu[0])+intval($ubu[1])*10;
                $plain[$location] = 1;
            }
        }
        $readyFleets;
        $plain = implode(";",$plain);
        $sql = "UPDATE games SET ".$me." = ? WHERE name = ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> bind_param("ss", $plain, $_SESSION["serverName"]);
        $stmt -> execute();
        $stmt -> close();
        $sql = "SELECT readyFleets FROM games WHERE name = ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> bind_param("s", $_SESSION["serverName"]);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($readyFleets);
        $stmt -> fetch();
        $stmt -> close();
        $readyFleets = explode(";", $readyFleets);
        if ($readyFleets[0] != $_SESSION['nickname'] && $readyFleets[1] != $_SESSION['nickname']) {
            if ($readyFleets[0] == "") {
                $readyFleets[0] = $_SESSION['nickname'];
            } else {
                $readyFleets[1] = $_SESSION['nickname'];
            }
        }
        $readyFleets = implode(";", $readyFleets);
        $sql = "UPDATE games SET readyFleets = ?, gameStart = ?, lastAction = ? WHERE name = ?";
        $stmt = $connection -> prepare($sql);
        $time = time();
        $stmt -> bind_param("ssss", $readyFleets, $time, $time ,$_SESSION["serverName"]);
        $stmt -> execute();
        $stmt -> close();
    } else {
        echo "Validacja nie udana >:";
    }
    mysqli_close($connection);
?>