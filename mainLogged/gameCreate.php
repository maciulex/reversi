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
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <title>Reversi!</title>
        <meta charset="utf-8">
        <link href="../styles/gameList/style.css" rel="stylesheet">
        <script>var motiveAccess = <?php  echo ((isset($_COOKIE['motive'])) ? $_COOKIE['motive'] : 0); ?>;</script>
        <script src="../mainApp.js"></script>
        <script>
            let password = false;
            function addPassword() {
                let place = document.querySelector(".hiddenPassword");
                if (password == false) {
                    place.removeAttribute("style");
                    password = true;
                } else {
                    place.setAttribute("style", "display: none");
                    password = false;
                }
            }
            function addCustom() {
                let place = document.querySelector("#custom");
                if (document.querySelector("#customL").checked) {
                    place.removeAttribute("style");
                    place.setAttribute("style", "height:150px");
                } else {
                    place.setAttribute("style", "display: none");
                }
            }
        </script>
    </head>
    <body>
        <header>
            <a href="../user/indexManagment/logout.php"><button>Wyloguj</button></a>
            <a href="profil.php" class="right"><button>Profil</button></a>
            <a href="index.php" class="right" style="width:75px"><button>Lista gier</button></a>
        </header>  
        <section class="gameCreateMainContainer">
            <main class="gameCreateMain">
                <h1>Stwórz grę</h1>
                <hr>
                <form action="app/createGame.php" method="POST">
                    <section><label for="name" required>Nazwa serwera: </label><input id="name" name="name" type="text"></section>
                    <section><label for="passwordCh">Hasło? </label><input id="passwordCh" name="passwordCh" type="checkbox" onclick="addPassword()"></section>
                    <section class="hiddenPassword" style="display: none"><label for="password">Hasło: </label><input id="password" name="password" type="text"></section>
                    <button>Utwórz</button>
                </form>
            </main>
        </section>
        <div class="changeMotive" onclick="changeMotive()"> 
            <img src="../photos/ico/sun-solid.svg">
        </div>
        <script>
            let error = <?php echo ((isset($_SESSION['error'])) ? '"'.$_SESSION['error'].'"' : "undefined");?>;
            if (error != undefined) {
                alert(error);
            }
            setMotive();
        </script>
    </body>
</html> 
<?php
    if (isset($_SESSION['error'])) {
        unset($_SESSION['error']);
    }
?>