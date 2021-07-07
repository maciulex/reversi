<?php
    include_once "../base.php";
    $mainIndexPath = "../index.php";
    session_start();

    if (!isset($_SESSION['nickname']) || !isset($_SESSION['authCode'])) {
        session_destroy();
        header("Location: ../index.php");
        exit();
    } else {
        @include_once "../user/loggedCheck.php";
    }
    $connection = @new mysqli($db_host, $db_user, $db_password, $db_name);
    if ($connection -> connect_errno > 0) {
        $_SESSION['error'] = "Error";
        header('Location: ../mainLogged/index.php');
        exit();
    } else {
        $sql = "SELECT inGame FROM users WHERE BINARY nickname = BINARY ?";
        $stmt = $connection -> prepare($sql);
        $stmt -> bind_param("s", $_SESSION['nickname']);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($inGame);
        $stmt -> fetch();
        if (empty($inGame) || !isset($inGame) || intval($inGame) == 0 || $inGame == "") {
            $_SESSION['error'] = "Error";
            header('Location: ../mainLogged/index.php');
            $stmt -> close();
            mysqli_close($connection);
            exit();
        }
        $stmt -> close();
        $page = "queue";
        include_once "app/imGame.php";
        mysqli_close($connection);
    }
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <title>kółko i krzyżyk!</title>
        <meta charset="utf-8">
        <link href="../styles/game/style.css" rel="stylesheet">
        <script src="app/queue/app.js"></script>
        <script>var motiveAccess = <?php  echo ((isset($_COOKIE['motive'])) ? $_COOKIE['motive'] : 0); ?>;</script>
        <script src="../mainApp.js"></script>
    </head>
    <body>
        <header class="noSelectText">
            <a href="../user/indexManagment/logout.php"><button>Wyloguj</button></a>
            <a href="../mainLogged/profil.php" class="right"><button>Profil</button></a>
            <a href="../mainLogged/index.php" class="right" style="width:75px"><button>Lista gier</button></a>
        </header>    
        <section class="mainQueue noSelectText">
            <aside class="noSelect">

            </aside>
            <main>

            </main>
        </section>
        <div class="changeMotive" onclick="changeMotive()"> 
        </div>
        <script>
            queueEngine(0);
            var intervals = setInterval(function(){queueEngine(0);}, 1000);
            setMotive();
        </script>
    </body>
</html>