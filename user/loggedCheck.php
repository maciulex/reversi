<?php
    $connection = @new mysqli($db_host, $db_user, $db_password, $db_name);
    if ($connection -> connect_errno > 0) {
        session_destroy();
        session_start();
        $_SESSION['error'] = "Mamy problem, proszę o zgłoszenie tego, error: 001";
        header("Location: ".$mainIndexPath);
        exit();
    } else {
        $sql = "SELECT authCode, authDate FROM users WHERE BINARY nickname = BINARY ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> bind_param("s", $_SESSION['nickname']);
        $stmt -> execute();
        $stmt -> store_result();
        $rows = $stmt -> num_rows;
        $stmt -> bind_result($authCode, $authDate);
        $stmt -> fetch();
        $stmt -> close();
        if ($rows == 0) {
            session_destroy();
            session_start();
            $_SESSION['error'] = "Mamy problem, Nie ma takiego użytkownika, error 002 ".$_SESSION['nickname'];
            @mysqli_close($connection);
            header("Location: ".$mainIndexPath);
            exit();
        } else {
            if ($authCode != $_SESSION['authCode'] || $authCode = "" || $authCode == NULL) {
                session_destroy();
                session_start();
                $_SESSION['error'] = "Proszę jeszcze raz się zalogować, error 003";
                @mysqli_close($connection);
                header("Location: ".$mainIndexPath);
                exit();
            } else {
                if (strtotime($authDate)+86400 < time()) {
                    $sql = "UPDATE users SET authCode = NULL, authDate = NULL WHERE BINARY nickname = BINARY ?";
                    $stmt = $connection -> prepare($sql);
                    $stmt -> bind_param("s", $_SESSION['nickname']);
                    $stmt -> execute();
                    $stmt -> close();

                    session_destroy();
                    session_start();
                    
                    $_SESSION['error'] = "Sesja wygasła proszę się zalogować ponownie";
                    @mysqli_close($connection);
                    header("Location: ".$mainIndexPath);
                    exit();
                }
                @mysqli_close($connection);
            }
        }
    }
?>