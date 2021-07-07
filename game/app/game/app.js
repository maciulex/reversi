function engine() {
    let xml = new XMLHttpRequest;
    xml.onreadystatechange = function () {
        if (this.status == 200 && this.readyState == 4) {
            let data = this.responseText.split(";;;");
            console.log(data);
            if (data[7] == "3") {
                window.location = "postGame.php?serverName="+server;
            }
            loadAside(data[0],data[1],data[2],data[5],data[6]);
            basicLoad(data[3]);
        }
    }
    xml.open("GET", "app/game/gameEngine.php?action=0", true);
    xml.send();
}

function basicLoad(board) {
    board = board.split(";");
    let place = document.querySelector(".main");
    let raw = "";
    for (let i = 0; i < 3; i++) {
        raw += "<tr>";
        for (let z = 0; z < 3; z++) {
            let addClass = "";
            if (board[i*3+z] == "1") {
                addClass = "circle";
            } else if (board[i*3+z] == "2") {
                addClass = "cross";
            }
            raw += `<td class="f${i*3+z} ${addClass}" onclick="guess(${i*3+z})"></td>`;
        }
        raw += "</tr>";
    }
    place.innerHTML = raw;
    let squere = document.querySelector("td").clientWidth;
    let stinki = document.querySelectorAll("td");
    for (let i = 0; i < stinki.length; i++) {
        stinki[i].style.height = squere+"px";
    }
}

function loadAside(serverName, playersNicks, whosTour, lastAction, timeNow, timeout = 300) {
    let timeLeft = (parseInt(timeNow)-parseInt(lastAction));
    let gameInfo = document.querySelector(".gameInfo");
    playersNicks = playersNicks.split(";");
    gameInfo.innerHTML = "<div>Nazwa serwera: "+serverName+"</div>";
    gameInfo.innerHTML += "<div>Gracze: "+playersNicks[0]+", "+playersNicks[1]+"</div>";
    gameInfo.innerHTML += "<div>Tura gracza: "+playersNicks[parseInt(whosTour)]+"</div>";
    gameInfo.innerHTML += "<div>Ostatni ruch: "+timeLeft+"s</div>";
    let infoP = document.querySelector(".blockInfo");
    if (myNick == playersNicks[parseInt(whosTour)]) {
        infoP.style.backgroundColor = "green";
        infoP.innerHTML = "Twoja tura";
    } else {
        infoP.style.backgroundColor = "red";
        infoP.innerHTML = "Poczekaj"; 
    }
    if (timeLeft > timeout) {
        gameInfo.innerHTML += `<button onclick="earlyEnd()">Zgłoś przedwczesne zakończenie gry</button>`;
    }
}
function guess(where) {
    let fild = document.querySelector(`.main .f${where}`).classList;
        if (!fild.contains("circle") && !fild.contains("cross")) {
            let xml = new XMLHttpRequest;
            xml.onreadystatechange = function () {
                if (this.status == 200 && this.readyState == 4) {
                    console.log(this.responseText);
                    engine();
                }
            }
            xml.open("GET", "app/game/gameEngine.php?action=1&cord="+where, true);
            xml.send();
        }
}

function earlyEnd() {
    let xml = new XMLHttpRequest;
    xml.open("GET", "app/game/gameEngine.php?action=2", true);
    xml.send();
}
engine();
let interval = setInterval(engine, 800);