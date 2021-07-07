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
    if (isset($_FILES['avatar']) && !empty($_FILES['avatar'])) {
        if ($_FILES['avatar']['size'] < 12 || $_FILES['avatar']['size'] > 2500000) {
            $_SESSION['error'] = "Nie właściwy rozmiar pliku";
            header("Location: ../profil.php");
            exit();
        }
        $fileExtension = exif_imagetype($_FILES['avatar']['tmp_name']);
        $extansion;
        if ($fileExtension != 2 && $fileExtension != 3){
            $_SESSION['error'] = "Nie właściwe rozszerzenie pliku".$fileExtension;
            header("Location: ../profil.php");
            exit();
        } else {
            if ($fileExtension == 2) {
                $extansion = ".jpg";
            } else {
                $extansion = ".png";
            }
        }
        $name;
        $connection = new mysqli($db_host, $db_user, $db_password, $db_name); 
        $sql = "SELECT id, avatar FROM users WHERE BINARY nickname = BINARY ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> bind_param("s", $_SESSION['nickname']);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($id, $avatar);
        $stmt -> fetch();
        if ($avatar == "" || empty($avatar)) {
            $name = hash("md2", $id, FALSE);
            $name = trim($name, "/");
            $name = trim($name, "\\");
            $name = trim($name, ".");
            $name .= $extansion;
        } else {
            $name = $avatar;
        }
        $stmt -> close();
        $sql = "UPDATE users SET avatar = ? WHERE BINARY nickname = BINARY ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> bind_param("ss", $name ,$_SESSION['nickname']);
        $stmt -> execute();
        $stmt -> close();
        move_uploaded_file($_FILES['avatar']['tmp_name'], realpath(dirname(dirname(getcwd())))."\photos\avatars\\".$name);
        echo $_FILES['avatar']['tmp_name'];
        mysqli_close($connection);
        header("Location: ../profil.php");
    }

?>