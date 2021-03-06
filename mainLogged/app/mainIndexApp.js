
function loadGames() {
    function goXML() {
        let sendData = getFormData();
        let xml = new XMLHttpRequest;
        xml.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                drawGames(this.responseText);
                adjustContainerSize();
            }
        }
        xml.open("GET", "app/getGamesList.php"+sendData, true);
        xml.send();
    }
    function getFormData() {
        let name = document.querySelector('input[name="gameName"]').value;
        let privacy = document.querySelector('select[name="gamePrivacy"]').value;
        let status = document.querySelector('select[name="gameStatus"]').value;
        let fullness = document.querySelector('select[name="gameFull"]').value;
        return "?name="+name+"&privacy="+privacy+"&status="+status+"&fullness="+fullness;
    }
    function drawGames(games) {
        let destination = document.querySelector(".gameList");
        destination.innerHTML = "";
        games = games.split(";;;");
        for (var i = 0; i < games.length-1; i++) {
            let localData = games[i].split(";;");
            if (localData[2] == '4') {
                var revange = "&code=revange";
            } else {
                var revange = "";
            }
            let rawGame = `                  
                <div class="gameListGame noSelectText">
                    <div class="gameListGameBase gameListGameName">
                        <div>${localData[0]}</div>
                    </div>
                    <div class="gameListGameBase gameListGamePlayers">
                        <div>${localData[3]}/2</div>
                    </div>
                    <div class="gameListGameBase gameListGameStatus">
                        <div>${getStatus(localData[2])}</div>
                    </div>
                    <div class="gameListGameBase gameListGamePassword">
                        <div>${getIco(localData[1])}</div>
                    </div>
                    <div class="gameListGameBase gameListGameJoin" onclick='gameJoin("${localData[0]}", "${localData[1]}", "${revange}")'>
                        <div>${getJoinText(localData[2])}</div>
                    </div>
                </div>`;
            destination.innerHTML += rawGame;
        }
        function getStatus(arg) {
            switch (arg) {
                case '1':
                    return "Nie rozpocz??ta";
                case '2':
                    return "Rozpocz??ta";
                case '3':
                    return "Zako??czona";
                case '4':
                    return "Poczekalnia rewan??u";
            }
        }
        function getJoinText(arg) {
            switch (arg) {
                case '1':
                    return "Do????cz";
                case '2':
                    return "Obserwuj";
                case '3':
                    return "Zako??czona";
                case '4':
                    return "Poczekalnia rewan??u";
            }
        }
        function getIco(arg) {
            switch (arg) {
                case '1':
                    return '<img src="../photos/ico/unlock.png">';
                case '2':
                    return '<img src="../photos/ico/padlock.png">';
            }
        }
    }
    function adjustContainerSize() {
        let link  = document.querySelector("main");
        let refer = document.querySelector("aside").clientHeight;
        let main  = document.querySelector(".gameList").clientHeight;
        if (refer < main) {
            link.classList.add("overFlowYScrool");
        } else {
            link.classList.remove("overFlowYScrool");
        }
    }
    goXML();
}
function gameJoin(name, password, revange = "") {
    if (password == "1") {
        window.location = "app/gameJoin.php?name="+name+revange;
    } else { 
        window.location = "app/gameJoin.php?name="+name+"&password="+prompt("Podaj has??o")+revange;
    }
}