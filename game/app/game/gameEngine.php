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
                echo "error 1";
                mysqli_close($connection);
                exit();
            } 
            $changesInBoardHasBeenDone = false;
            $playersNicks; $whosTour; $status; $board; $wholeGameScore;
            $coordinate = intval($_GET["cord"]);
            $sql = "SELECT playersNicks, whosTour, status, board, score FROM reversi WHERE name = ?";
            $stmt = $connection -> prepare($sql);
            $stmt -> bind_param("s", $_SESSION['serverName']);
            $stmt -> execute();
            $stmt -> store_result();
            $stmt -> bind_result($playersNicks, $whosTour, $status, $board, $wholeGameScore);
            $stmt -> fetch();
            $playersNicks = explode(";", $playersNicks);
            $whosTour = intval($whosTour);
            $board = explode(";", $board);
            if ($status != "2" || $playersNicks[$whosTour] != $_SESSION['nickname'] || $board[$coordinate] != "0") {
                echo "error wrong 002";
                $stmt -> close();
                mysqli_close($connection);
                exit();
            }
            $stmt -> close();
            echo "1 done<d1>";
            $directions = array(-8, 8, -9, -7, 7, 9, -1, 1);
            
            $enemyNumber = (($whosTour == 0) ? "2" : "1");
            $myNumber =    (($whosTour == 0) ? "1" : "2");
            
            $board[$coordinate] = $myNumber;

            foreach ($directions as $key) {
                $localCordinateCopy = $coordinate;
                $enemyLine = array();
                $lineMultiplayer = 1;
                $lineReady = false;
                $statement = true;
                while ($statement) {
                    $nextPlace = $localCordinateCopy + ($key);
                    $nextPlaceRow = ceil(($nextPlace+1)/8);
                    echo "key: ".$key." Np: ".$nextPlace." NpR: ".$nextPlaceRow." Cordinate: ".$localCordinateCopy;
                    if ($nextPlace < 0 || $nextPlace > 64) {
                        $statement = false;
                        echo " quit 0";
                    }
                    $sameRowCheck = ($nextPlaceRow != ceil(($localCordinateCopy+1)/8) || $nextPlaceRow != ceil(($localCordinateCopy-1)/8)) ? true : false;
                    if ($key != -1 && $key != 1) {
                        if (($nextPlaceRow > ceil(($localCordinateCopy+1)/8)+1 || $nextPlaceRow < ceil(($localCordinateCopy+1)/8)-1) && !$sameRowCheck) {
                            $statement = false;
                            echo " quit 1";
                        }
                    } else {
                        if ($sameRowCheck) {
                            $statement = false;
                            echo " quit 2";
                        }
                    }
                    if ($board[$nextPlace] == $enemyNumber && $statement) {
                        $enemyLine[] = $nextPlace;          
                        $lineMultiplayer = 2;
                        $localCordinateCopy = $nextPlace;
                    } else if ($board[$nextPlace] == $myNumber && $statement) {
                        $statement = false;
                        $lineReady = true;
                        echo " quit 3";
                    } else {
                        $statement = false;
                        echo " quit 4";
                    }
                    echo "<d1>";
                }
                if ($lineReady && !empty($enemyLine)) {
                    foreach ($enemyLine as $toChange) {
                        $changesInBoardHasBeenDone = true;
                        $board[$toChange] = $myNumber;
                    } 
                }
            }
            if (!$changesInBoardHasBeenDone) {
                echo "error wrong 003";
                mysqli_close($connection);
                exit();
            }
            $win = array(false, "", -1);
            $stats = array(0,0);
            $score = array(0,0);
            foreach ($board as $key) {
                if ($key == "1") {
                    $score[0] += 1;
                } else if ($key == "2") {
                    $score[1] += 1;
                } 
            }

            if ($score[0]+$score[1] == 64) {
                if ($score[0] > $score[1]) {
                    $win[0] = true;
                    $win[1] = "Wygrał gracz: ".$playersNicks[0];    
                    $win[2] = 0;
                    $stats[0] = 1;      
                } else if ($score[0] < $score[1]) {
                    $win[0] = true;
                    $win[1] = "Wygrał gracz: ".$playersNicks[1];   
                    $win[2] = 1;
                    $stats[1] = 1;  
                } else {
                    $win[0] = true;
                    $win[1] = "Remis";
                    $win[2] = -1;
                }
            }
            if ($score[0] == 0) {
                $win[0] = true;
                $win[1] = "Wygrał gracz: ".$playersNicks[1];    
                $win[2] = 1;
                $stats[1] = 1;  
            } else if ($score[1] == 0) {
                $win[0] = true;
                $win[1] = "Wygrał gracz: ".$playersNicks[0];       
                $win[2] = 0;
                $stats[0] = 1;  
            }

            $whosTour = ($whosTour == 0) ? 1 : 0;

            $board = implode(';',$board);
            $score = implode(';',$score);

            $TIME = time();
            $sql = "UPDATE reversi SET whosTour = ?, lastAction = ?, board = ?, activeGameScore = ? WHERE name = ?";
            $stmt = $connection -> prepare($sql);
            $stmt -> bind_param("idsss", $whosTour, $TIME, $board, $score,$_SESSION['serverName']);
            $stmt -> execute();
            $stmt -> close();
            if ($win[0]) {
                if ($win[2] != -1) {
                    $wholeGameScore = explode(";",$wholeGameScore);
                    $wholeGameScore[$win[2]] = intval($wholeGameScore[$win[2]])+1;
                    $wholeGameScore = implode(";", $wholeGameScore);
                    $sql = "UPDATE users SET inGame = 0, Sgames = Sgames + 1, SgamesWin = SgamesWin + ?, SgamesLose = SgamesLose + ? WHERE nickname = ?";
                    $stmt = $connection -> prepare($sql);
                    $stmt -> bind_param("iis", $stats[0], $stats[1], $playersNicks[0]);
                    $stmt -> execute();
                    $stmt -> bind_param("iis", $stats[1], $stats[0], $playersNicks[1]);
                    $stmt -> execute();
                    $stmt -> close();
                } else {
                    $sql = "UPDATE users SET inGame = 0, Sgames = Sgames + 1, SgamesDraw = SgamesDraw + 1 WHERE nickname = ?";
                    $stmt = $connection -> prepare($sql);
                    $stmt -> bind_param("s", $playersNicks[0]);
                    $stmt -> execute();
                    $stmt -> bind_param("s", $playersNicks[1]);
                    $stmt -> execute();
                    $stmt -> close();
                }
                $sql = "UPDATE reversi SET status = 3, gameEnd = ?, score = ? WHERE name = ?";
                $stmt = $connection -> prepare($sql);
                $stmt -> bind_param("sss", $win[1], $wholeGameScore, $_SESSION['serverName']);
                $stmt -> execute();
                $stmt -> close();

            }
            // for ($i = -4; $i < 5; $i++) {
            //     ($i == 0) ? continue : "";
            //     if ($board[$coordinate]+$i == $enemyNumber) {

            //     }
            // }
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