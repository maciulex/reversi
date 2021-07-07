<?php
    session_start();
    if (!isset($_SESSION['nickname']) || !isset($_SESSION['authCode'])) {
        session_destroy();
        session_start();
        $_SESSION['error'] = "Coś się nie udało!";
        header("Location: ../../index.php");
        exit();
    } else {
        @include_once "../../base.php";
        $connection = @new mysqli($db_host, $db_user, $db_password, $db_name);
        if ($connection -> connect_errno > 0) {
            @mysqli_close($connection);
            $_SESSION['error'] = "Mamy problem, proszę o zgłoszenie tego, error: 001";
            header("Location: ../../index.php");
            exit();
        } else {
            $sql = "UPDATE users SET authCode = NULL, authDate = NULL WHERE BINARY nickname = BINARY ?";
            $stmt = $connection -> prepare($sql);
            $stmt -> bind_param("s", $_SESSION['nickname']);
            $stmt -> execute();
            $stmt -> close();
            @mysqli_close($connection);
            header('Location: ../../index.php');
            session_destroy();
            exit();
        }
        @mysqli_close($connection);
    }

?>