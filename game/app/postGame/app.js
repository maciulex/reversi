var playersNumber = 0;
var data = [undefined,undefined,undefined];

function postGameEngine() {
    function xmlEngine() {
        var xml = new XMLHttpRequest;
        xml.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText[0] == "e" && this.responseText[4] == "r") {
                    alert("wystąpił błąd ");
                    return;
                }
                dealData(this.responseText);
            }
        }
        xml.open("GET", "app/postGame/postGame.php?serverName="+server, true);
        xml.send();
    }
    function dealData(arg) {
        arg = arg.split(";;;");
        data[0] = arg[0].split(";");
        data[1] = arg[1].split(";;");
        data[2] = arg[2].split(";");
        loadPages();
        //data[2] = arg[0];
        //gameLoad(arg[0]);
        //playerLoad(arg[1]);
    }
    xmlEngine();
}


function loadPages() {
    let place = document.querySelector(".mainQueue main");
    place.innerHTML = "";
    function playerLoad(arg) {
        let localData = arg.split(";");
        console.log(localData);
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
                        <h1>${localData[1]}</h1>
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
        if (localData[0] == "true") {
            document.querySelector(".mainQueue aside").innerHTML += `<button onclick='window.location = "../mainLogged/app/gameJoin.php?name=${server}&code=revange"'>Rewanż</button>`;
        }
    }
    function gameLoad() {
        let place = document.querySelector(".mainQueue aside");
        let players = "Gracze: " + data[0][8] + ", " + data[0][9];
        let score = "Wynik: " + data[0][6] + " : " + data[0][7];
        place.innerHTML = `
            <br><br>
            Nazwa gry: ${data[0][0]}<br>
            Prywatność: ${getPrivacy(data[0][2])}<br>
            Status: ${getStatus(data[0][1])}<br>
            Graczy: ${data[0][3]}/2<br>
            Host: ${data[0][4]}<br>
            ${data[0][5]}<br>
            ${players}<br>
            ${score}<br>

            <div class="buttonForChange"></div>
        `;
        function getStatus(arg) {
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
    gameLoad();
    for (let i = 0; i < 2; i++) {
        playerLoad(data[1][i]);
    }
    document.querySelector(".buttonForChange").innerHTML = `<button onclick="changeCard(1)">Pokaż planszę</button>`;
}
function drawBoards() {
    let place = document.querySelector("main");
    document.querySelector("main").innerHTML = "";
    let value = data[2];
    let raw = `<section class="board"><table><tbody>`;
    for (let i = 0; i < 9; i++) {
        if (i == 0 || i == 3 || i == 6) {
            raw += '</tr><tr>';
        }
        switch (value[i]){
            case "0":
                raw += `<td class="tdElement"></td>`;
            break;
            case "1":
                raw += `<td class="tdElement circle"></td>`;
            break;
            case "2":
                raw += `<td class="tdElement cross"></td>`;
            break;
        }   
    }
    raw += "</tbody></table></section>";
    document.querySelector("main").innerHTML = raw;
    let width = document.querySelector(".tdElement").clientWidth;
    let elements = document.querySelectorAll(".tdElement");
    for (let i = 0; i < elements.length; i++) {
        elements[i].style.height = width+"px";
    }
}
function changeCard(arg) {
        let buttonPlace = document.querySelector(".buttonForChange");
    switch (arg) {
        case 0:
            loadPages();
            buttonPlace.innerHTML = `<button onclick="changeCard(1)">Pokaż planszę</button>`;
        break;
        case 1:
            drawBoards();
            buttonPlace.innerHTML = `<button onclick="changeCard(0)">Pokaż graczy</button>`;
        break;
    }
}