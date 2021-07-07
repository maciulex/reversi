<?php
    session_start();
    if (!isset($_POST['nick']) || !isset($_POST['password']) || !isset($_POST['password2'])) {
        $_SESSION['error'] = "Nie udało się zarejestrować!";
        header("Location: ../../index.php");
        exit();
    } else {
        if (trim($_POST['nick']) != $_POST['nick']) {
            $_SESSION['error'] = "Nick nie może mieć spacji!";
            header("Location: ../../index.php");
            exit();
        } else if (trim($_POST['nick'], ";") != $_POST['nick']) {
            $_SESSION['error'] = 'Nick nie może zawierać: ;';
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
                $sql = "SELECT nickname FROM users WHERE BINARY nickname = BINARY ?";
                $stmt = $connection -> prepare($sql);
                $stmt -> bind_param("s", $_POST['nick']);
                $stmt -> execute();
                $stmt -> store_result();
                $rows = $stmt -> num_rows;
                $stmt -> close();
                if ($rows == 0) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $email = ((!isset($_POST['email']) || $_POST['email'] == "") ? null : $_POST['email']);
                    $sql = "INSERT INTO users (nickname, passcode, email) VALUES (?, ?, ?)";
                    $stmt = $connection -> prepare($sql);
                    $stmt -> bind_param("sss", $_POST['nick'], $password, $email);
                    $stmt -> execute();
                    $stmt -> close();
                    $_SESSION['error'] = "Udało się zarejestrować!";
                } else {
                    $_SESSION['error'] = "Istnieje użytkownik o tym nicku!";    
                }
                @mysqli_close($connection);
                header('Location: ../../index.php');
                exit();
            }
            @mysqli_close($connection);
        }
    }


?>