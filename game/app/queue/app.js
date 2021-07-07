var status;
var host = false;
var playersNumber = 0;
function queueEngine(arg) {
    function xmlEngine() {
        var xml = new XMLHttpRequest;
        xml.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText[0] == "e" && this.responseText[4] == "r") {
                    console.log(this.responseText);
                    alert("wystąpił błąd ");
                    return;
                }
                switch (arg) {
                    case 0:
                        dealData(this.responseText);
                    break;
                    case 1:
                        window.location = "../mainLogged/";
                    break;
                }
            }
        }
        xml.open("GET", "app/queue/queueEngine.php?action="+arg, true);
        xml.send();
    }
    function dealData(arg) {
        arg = arg.split(";;;");
        gameLoad(arg[0]);
        playerLoad(arg[1]);
        if (host) {
            hostLoad();
        }
        if (status == "4" && host) {
            document.querySelector("#throwOut").parentNode.innerHTML="";
        }
    }
    function playerLoad(data) {
        let iStillHERE = false;
        let place = document.querySelector(".mainQueue main");
        let kick = "";
        place.innerHTML = "";
        data = data.split(";;");
        playersNumber = data.length-1;
        for (var i = 0; i < data.length-1; i++) {
            let localData = data[i].split(";"); 
            if (localData[0] == "true") {
                if (i == 0) host = true;
                iStillHERE = true;
                kick = `<button onclick="queueEngine(1)">Opuść grę</button>`;
            } else if (host && localData[0] == "false") {
                kick = `<button onclick="queueEngine(2)" id="throwOut">Wyrzuć gracza</button>`;
            }
            if (localData[3] == "false") {
                var avatar = "def.jpg"; 
            } else {
                var avatar = localData[3]; 
            }
            let raw = `
                <div class="player">
                    <section class="header">
                        <img src="../photos/avatars/${avatar}">
                        <section>
                            <h1>${localData[1]} <br> <div>${kick}</div></h1>
                            <div>
                                Opis gracza: ${localData[2]}
                            </div>
                        </section>
                    </section>
                    <section class="stats">
                        <div>Rozegrane: <br> ${localData[4]}</div><div>Wygrane: <br> ${localData[5]}</div><div>Przegrane: <br> ${localData[6]}</div>
                        <div>Porzucone: <br> ${localData[7]}</div><div>Wcześnie zakończone: <br> ${localData[8]}</div><div>Remisy: <br> ${localData[9]}</div>
                    </section>
                </div>
            `;
            place.innerHTML += raw; 
        }
        if (!iStillHERE) {
            window.location = "../mainLogged/";
        }
    }
    function gameLoad(data) {
        data = data.split(";");
        status = data[1];
        if (data[1] == "2") {
            window.location = "battleField.php";
        }
        let place = document.querySelector(".mainQueue aside");
        place.innerHTML = `
            <br><br>
            Nazwa gry: ${data[0]}<br>
            Prywatność: ${getPrivacy(data[2])}<br>
            Status: ${getStatus(data[1])}<br>
            Graczy: ${data[3]}/2<br>
            Host: ${data[4]}<br>
            <div class="hostOption"></div>
        `;
        function getStatus(arg) {
            if (playersNumber == 2 && arg == '1') {
                return "Oczekiwanie na rozpoczęcie przez hosta";
            }
            switch (arg) {
                case '1':
                    return "Nie rozpoczęta";
                case '2':
                    return "Rozpoczęta";
                case '3':
                    return "Zakończona";
                case '4':
                    return "Poczekalnia rewanżu";
            }
        }
        function getPrivacy(arg) {
            switch (arg) {
                case '1':
                    return "Publiczna";
                case '2':
                    return "Nie publiczna";
            }
        }
    }
    function hostLoad() {
        if (playersNumber == 2) {
            document.querySelector(".hostOption").innerHTML = `<button onclick="queueEngine(3)">Rozpocznij grę</button>`;
        }
    }
    xmlEngine();
}

function earlyEnd() {
    let xml = new XMLHttpRequest;
    xml.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
        } 
    }
    xml.open("GET", "app/game/gameEngine.php?action=2", true);
    xml.send();
}