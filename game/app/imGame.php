<?php 
    function gotoF($arg) {
        switch ($arg) {
            case "1":
            case "4":
                header("Location: gameQueue.php");
            break;
            case "2":
                header("Location: battleField.php");
            break;
            case "3":
                header("Location: postGame.php");
            break;
        }
    }
    $inGame;
    $sql = "SELECT inGame FROM users WHERE BINARY nickname = BINARY ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> bind_param("s", $_SESSION['nickname']);
    $stmt -> execute();
    $stmt -> store_result();
    $stmt -> bind_result($inGame);
    $stmt -> fetch();
    if ((empty($inGame) || !isset($inGame) || intval($inGame) == 0 || $inGame == "") && $page != "endGame") {
        $_SESSION['error'] = "Error";
        header('Location: ../mainLogged/index.php');
        $stmt -> close();
        mysqli_close($connection);
        exit();
    }
    $stmt -> close();
    $sql = "SELECT status FROM gamestictactoe WHERE id = ?";
    $stmt = $connection -> prepare($sql);
    $stmt -> bind_param("i", $inGame);
    $stmt -> execute();
    $stmt -> store_result();
    $stmt -> bind_result($status);
    $stmt -> fetch();
    $stmt -> close();
    switch ($status) {
        case "1":
        case "4":
            if ($page != "queue") {
                gotoF($status);
            }
        break;
        case "2":
            if ($page != "battleField") {
                gotoF($status);
            }
        break;
        case "3":
            if ($page != "endGame") {
                gotoF($status);
            }
        break;
    }
?>