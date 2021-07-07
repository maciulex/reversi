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
    }
    $page = "battleField";
    include_once "app/imGame.php";
    mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <title>kółko i krzyżyk!</title>
        <meta charset="utf-8">
        <link href="../styles/game/style.css" rel="stylesheet">
        <script>
            var server = "<?php echo $_SESSION['serverName'];?>";
            var myNick = "<?php echo $_SESSION['nickname'];?>";
            var motiveAccess = <?php  echo ((isset($_COOKIE['motive'])) ? $_COOKIE['motive'] : 0); ?>;
        </script>
        <script src="../mainApp.js"></script>
        <script src="app/game/app.js"></script>
    </head>
    <body>
        <header class="noSelectText">
            <a href="../user/indexManagment/logout.php" style="float: left;"><button>Wyloguj</button></a>
            <section class="blockInfo" style="float: left;">
            </section>
            <a href="../mainLogged/profil.php" class="right"><button>Profil</button></a>
            <a href="../mainLogged/index.php" class="right" style="width:75px"><button>Lista gier</button></a>
        </header>    
        <section class="batteField noSelectText">
            <aside>
                <main class="gameInfo">

                </main>
                <main>
                </main>
            </aside>
            <section>
                <table class="main">
                    <tbody>

                    </tbody>
                </table>
            </section>
        </section>
        <div class="changeMotive" onclick="changeMotive()"> 
        </div>
        <script>
            setMotive();
        </script>
    </body>
    <div id="pickedUp"></div>
</html>