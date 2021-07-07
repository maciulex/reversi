<?php
    include_once "../../base.php";
    $mainIndexPath = "../../index.php";
    session_start();
    if (!isset($_SESSION['nickname']) || !isset($_SESSION['authCode'])) {
        session_destroy();
        header("Location: ../../index.php");
        exit();
    } else {
        @include_once "../../user/loggedCheck.php";
    }
    if (!isset($_POST['name']) || empty($_POST['name']) || strlen($_POST['name']) < 3 || trim($_POST['name'],";") != $_POST['name']) {
        $_SESSION['error'] = "Coś się nie udało! 1";
        header("Location: ../gameCreate.php");
        exit();
    }   
    $passwordBool = false;
    $password;
    if (isset($_POST['passwordCh'])) {
        $password = $_POST['password'];
        if ($password != trim($password,";")) {
            $_SESSION['error'] = "Nie dozwolony znak: ;";
            header("Location: ../gameCreate.php");
            exit();
        }
        $passwordBool = true;
    } else {
        $passwordBool = false;
    }
    $connection = @new mysqli($db_host,$db_user,$db_password,$db_name);
    if ($connection -> connect_errno > 0) {
        $_SESSION['error'] = "Coś się nie udało!";
        header("Location: ../gameCreate.php");
        exit();
    } else {
        $location;
        $sql = "SELECT id FROM reversi WHERE BINARY name = ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> bind_param("s", $_POST['name']);
        $stmt -> execute();
        $stmt -> store_result();
        $numRows = $stmt -> num_rows;
        $stmt -> close();
        if ($numRows > 0) {
            $_SESSION['error'] = "Serwer z taką nazwą istnieje!";
            header("Location: ../gameCreate.php");
            exit();
        }
        if ($passwordBool) {
            $sql = "INSERT INTO reversi (name,password,privacy) VALUES (?,?,2)";
            $stmt = $connection -> prepare($sql);
            $stmt -> bind_param("ss", $_POST['name'], $password);
            $stmt -> execute();
            $stmt -> close();
            $location = "Location: gameJoin.php?name=".$_POST['name']."&password=".$password;
        } else {
            $sql = "INSERT INTO reversi (name,privacy) VALUES (?,1)";
            $stmt = $connection -> prepare($sql);
            $stmt -> bind_param("s", $_POST['name']);
            $stmt -> execute();
            $stmt -> close();
            $location = "Location: gameJoin.php?name=".$_POST['name'];
        }
        mysqli_close($connection);
        header($location);
        exit();
    }
    mysqli_close($connection);
?>