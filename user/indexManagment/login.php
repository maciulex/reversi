<?php
    session_start();
    if (!isset($_POST['nick']) || !isset($_POST['password'])) {
        $_SESSION['error'] = "Nie udało się zalogować!";
        header("Location: ../../index.php");
        exit();
    } else {
        if (trim($_POST['nick']) != $_POST['nick']) {
            $_SESSION['error'] = "Nick nie może mieć spacji!";
            header("Location: ../../index.php");
            exit();
        } else {
            @include_once "../../base.php";
            $connection = @new mysqli($db_host, $db_user, $db_password, $db_name);
            if ($connection -> connect_errno > 0) {
                $_SESSION['error'] = "Mamy problem, proszę o zgłoszenie tego, error: 001";
                header("Location: ../../index.php");
                exit();
            } else {
                $sql = "SELECT passcode FROM users WHERE BINARY nickname = BINARY ?";
                $stmt = $connection -> prepare($sql);
                $stmt -> bind_param("s", $_POST['nick']);
                $stmt -> execute();
                $stmt -> store_result();
                $stmt -> bind_result($password);
                $stmt -> fetch();
                $rows = $stmt -> num_rows;
                $stmt -> close();
                if ($rows > 0) {
                    if (password_verify($_POST['password'], $password)) {
                        $header = 0;
                        $auth = password_hash(rand(0,999),PASSWORD_DEFAULT);
                        $date = date('Y-m-d H:i:s');
                        $sql = 'UPDATE users SET authCode = ?, authDate = ? WHERE BINARY nickname = BINARY ?';
                        $stmt = $connection -> prepare($sql);
                        $stmt -> bind_param("sss", $auth, $date, $_POST['nick']);
                        $stmt -> execute();
                        $stmt -> close();
                        $sql = "SELECT inGame FROM users WHERE BINARY nickname = BINARY ?";
                        $stmt = $connection -> prepare($sql);
                        $stmt -> bind_param("s", $_POST['nick']);
                        $stmt -> execute();
                        $stmt -> store_result();
                        $stmt -> bind_result($inGame);
                        $stmt -> fetch();
                        if (intval($inGame) != 0) {
                            $sql = "SELECT name, status FROM gamestictactoe WHERE id = $inGame";
                            $result = $connection -> query($sql);
                            $row = $result -> fetch_object();
                            $_SESSION['serverName'] = $row -> name;
                            $status = intval($row -> status);
                            if ($status == 1) {
                                $header = 1;
                            }
                        }
                        $stmt -> close();

                        $_SESSION['authCode'] = $auth;
                        $_SESSION['nickname'] = $_POST['nick'];
                        @mysqli_close($connection);
                        switch ($header) {
                            default:
                            case 0:
                                header('Location: ../../mainLogged/index.php');
                            break;
                            case 1:
                                header('Location: ../../game/gameQueue.php');
                            break;
                        }
                        exit();
                    } else {
                        $_SESSION['error'] = "Nie poprawne hasło!";   
                    }
                } else {
                    $_SESSION['error'] = "Nie istnieje użytkownik o tym nicku!";    
                }
                @mysqli_close($connection);
                header('Location: ../../index.php');
                exit();
            }
            @mysqli_close($connection);
        }
    }

?>