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
            $sql = "SELECT name, playersNicks, whosTour, timeout, lastAction, status, board FROM gamestictactoe WHERE name = ?";
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
            if (!isset($_GET["cord"]) || intval($_GET["cord"]) < 0 || intval($_GET["cord"]) > 8) {
                echo "error 3";
                mysqli_close($connection);
                exit();
            } 
            $playersNicks;$whosTour;$status;$board;$score;
            $cord = intval($_GET["cord"]);
            $sql = "SELECT playersNicks, whosTour, status, board, score FROM gamestictactoe WHERE name = ?";
            $stmt = $connection -> prepare($sql);
            $stmt -> bind_param("s", $_SESSION['serverName']);
            $stmt -> execute();
            $stmt -> store_result();
            $stmt -> bind_result($playersNicks, $whosTour, $status, $board, $score);
            $stmt -> fetch();
            if ($status != "2") {
                echo "error 4";
                $stmt -> close();
                mysqli_close($connection);
                exit();
            } else {
                $whosTour = intval($whosTour);
                $playersNicks = explode(";", $playersNicks);
                if ($playersNicks[$whosTour] != $_SESSION['nickname']) {
                    echo "error 5";
                    $stmt -> close();
                    mysqli_close($connection);
                    exit();
                }
                $board = explode(";", $board);
                for ($i = 0; $i < 9; $i++) {
                    $board[$i]=intval($board[$i]);
                }
                if ($board[$cord] == 0) {
                    if ($whosTour == 0) {
                        $board[$cord] = 1;
                        $whosTour = 1;
                    } else {
                        $board[$cord] = 2;
                        $whosTour = 0;
                    }
                    $boardI = implode(";", $board);
                    $time = time();
                    $sql = "UPDATE gamestictactoe SET board = ?, whosTour = ?, lastAction = ? WHERE name = ?";
                    $stmt -> prepare($sql);
                    $stmt -> bind_param("sids", $boardI, $whosTour, $time, $_SESSION["serverName"]);
                    $stmt -> execute();
                    echo "done";
                    $win = array(false, 0);
                    //lazy shit (*) ~~ code
                    if ($board[0] != 0 && $board[0] == $board[4] && $board[4] == $board[8]) {
                        $win[0] = true;
                        $win[1] = $board[0];
                    } else if ($board[2] != 0 && $board[2] == $board[4] && $board[4] == $board[6]) {
                        $win[0] = true;
                        $win[1] = $board[2];
                    } else if ($board[0] != 0 && $board[0] == $board[1] && $board[1] == $board[2]) {
                        $win[0] = true;
                        $win[1] = $board[0];
                    } else if ($board[3] != 0 && $board[3] == $board[4] && $board[4] == $board[5]) {
                        $win[0] = true;
                        $win[1] = $board[3];
                    } else if ($board[6] != 0 && $board[6] == $board[7] && $board[7] == $board[8]) {
                        $win[0] = true;
                        $win[1] = $board[6];
                    } else if ($board[0] != 0 && $board[0] == $board[3] && $board[3] == $board[6]) {
                        $win[0] = true;
                        $win[1] = $board[0];
                    } else if ($board[1] != 0 && $board[1] == $board[4] && $board[4] == $board[7]) {
                        $win[0] = true;
                        $win[1] = $board[1];
                    } else if ($board[2] != 0 && $board[2] == $board[5] && $board[5] == $board[8]) {
                        $win[0] = true;
                        $win[1] = $board[2];
                    }
                    if (!$win[0]) {
                        $win[0] = true;
                        $win[1] = "Remis";
                        for ($i = 0; $i < 9; $i++) {
                            if ($board[$i] == 0) {
                                $win[0] = false;
                                break;
                            }
                        }
                    } else {
                        $winMess = "Wygrał gracz: ".$playersNicks[intval($win[1])-1];
                    }
                    if ($win[0]) {
                        $win[1] = intval($win[1])-1;
                        $score = explode(";", $score);
                        $score[$win[1]] = intval($score[$win[1]])+1;
                        $score = implode(";", $score);
                        $sql = "UPDATE gamestictactoe SET status = 3, gameEnd = ?, score = ?, revange = 0 WHERE name = ?";
                        $stmt -> prepare($sql);
                        $stmt -> bind_param("sss", $winMess, $score, $_SESSION["serverName"]);
                        $stmt -> execute();

                        $stats = array(0,0,0);
                        if ($win[1] == -1) {
                            $stats[2] = 1;    
                        } else if ($win[1] == 0) {
                            $stats[0] = 1;
                        } else {
                            $stats[1] = 1;
                        }
                        $sql = "UPDATE users SET inGame = 0, Sgames = Sgames + 1, SgamesWin = SgamesWin + ?, SgamesLose = SgamesLose + ?, SgamesDraw = SgamesDraw + ? WHERE nickname = ?";
                        $stmt = $connection -> prepare($sql);
                        $stmt -> bind_param("iiis", $stats[0], $stats[1], $stats[2], $playersNicks[0]);
                        $stmt -> execute();
                        $stmt -> bind_param("iiis", $stats[1], $stats[0], $stats[2], $playersNicks[1]);
                        $stmt -> execute();
                        $stmt -> close();
                    }
                } else {
                    echo "error 6";
                    $stmt -> close();
                    mysqli_close($connection);
                    exit();
                }
            }
        break;
        case 2:
            //early end >:
            $lastAction; $playersNicks; $status;
            $sql = "SELECT lastAction, playersNicks, status FROM gamestictactoe WHERE name = ?";
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
                    $sql = 'UPDATE gamestictactoe SET status = 3, gameEnd = CONCAT("Po 5 min bezczynności gra zakończona za życzenie gracza: ", ?) WHERE name = ?';
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