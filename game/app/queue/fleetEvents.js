document.addEventListener("mousemove", function (ev) {
    let clientX = ev.clientX;
    let clientY = ev.clientY;
    if (clientX != undefined) {
        lastCords[0] = clientX;
        lastCords[1] = clientY;
    } else {
        clientX = lastCords[0];
        clientY = lastCords[1];
    }
    if(Date.now() - lastMove > 15) {
        if (track[0]) {
            let table = document.querySelector("#busyShips");
            document.querySelector("#pickedUp").style.top = (clientY-(.5*width))+'px';
            document.querySelector("#pickedUp").style.left = (clientX-((.5*track[4])*width))+'px'; 
            var reset = document.querySelectorAll(".whileShip");
            if (reset.length != 0) {
                for (let i = 0; i < reset.length; i++) {
                    reset[i].classList.remove("whileShip");
                }
            }
            if ((elementB[1] < clientX && elementB[2] < clientY) && (elementB[1]+elementB[3] > clientX && elementB[2]+elementB[4] > clientY)) {
                let row = Math.floor((clientY-elementB[2])/width);
                let cell = Math.floor((clientX-elementB[1])/width);
                if (track[3] == 0) {
                    var validate = true;
                    for (let i = Math.round(-1*(track[4]/2)); i < Math.round(track[4]/2); i++) {
                        if (!(row < 0 || row > size[0] || cell+i < 0 || cell+i > size[1])) {
                            if (table.rows[row].cells[cell+i].classList.contains("solidShip")) {
                                validate = false;
                                break;
                            }
                        } else {
                            validate = false;
                            break;
                        }
                    }
                    if (validate) {
                        for (let i = Math.round(-1*(track[4]/2)); i < Math.round(track[4]/2); i++) {
                            table.rows[row].cells[cell+i].classList.add("whileShip");
                            validation(0);
                        }
                    } else {
                        validation(1);
                    }
                } else {
                    var validate = true;
                    for (let i = Math.round(-1*(track[4]/2)); i < Math.round(track[4]/2); i++) {
                        if (!(row+i < 0 || row+i > size[0] || cell < 0 || cell > size[1])) {
                            if (table.rows[row+i].cells[cell].classList.contains("solidShip")) {
                                validate = false;
                                break;
                            }
                        } else {
                            validate = false;
                            break;
                        }
                    }
                    if (validate) {
                        for (let i = Math.round(-1*(track[4]/2)); i < Math.round(track[4]/2); i++) {
                            table.rows[row+i].cells[cell].classList.add("whileShip");
                            validation(0);
                        }
                    } else {
                        validation(1);
                    }
                }
                function validation(arg) {
                    switch(arg) {
                        case 0:
                            document.querySelector("#pickedUp").classList.add("displayOff");
                        break;
                        case 1:
                            document.querySelector("#pickedUp").classList.remove("displayOff");
                        break;
                    }
                }
            } else {
                document.querySelector("#pickedUp").classList.remove("displayOff");
            }
        } 
        lastMove = Date.now();
    }
});
document.addEventListener("keypress", function (ev) {
    if (ev.keyCode == 114 && track[0]) {    
        let place = document.querySelector("#pickedUp");
        var event = new Event("mousemove");
        if (track[3] == 0) {
            place.classList.add("rotation90");
            track[3] = 1;
            document.dispatchEvent(event);
        } else {
            place.classList.remove("rotation90");
            track[3] = 0;
            document.dispatchEvent(event);
        }
    }
});
document.addEventListener("click", function (ev) {
    var waitingShips = document.querySelectorAll(".whileShip");
    if (waitingShips.length > 0) {
        for (let i = 0; i < waitingShips.length; i++) {
            waitingShips[i].classList.add(`solidShip`, `shipNo${track[1]}`, `rotation${track[3]}`);
            waitingShips[i].classList.remove("whileShip");
            waitingShips[i].setAttribute("onclick", `deleteShip(${track[1]})`);
        }
        mouseClickHelper = false;
        var add = document.querySelectorAll(".pickedUp");
        for (let i = 0; i < add.length; i++) {
            add[i].classList.add("itsPlaced");
        }
        clear();
        setShips += 1;
        if (setShips == shipsData.length) {
            document.querySelector(".doVal").innerHTML = `<button onclick="shipsValidation()")>Waliduj</button>`;
        }
    } else if (track && mouseClickHelper) {
        mouseClickHelper = false;
        clear();
    } else {
        mouseClickHelper = true;
    }
    function clear() {
        track = [false,0,0,0,0];
        var clear = document.querySelectorAll(".pickedUp");
        for (let i = 0; i < clear.length; i++) {
            clear[i].classList.remove("pickedUp");
        }
        document.querySelector("#pickedUp").classList.add("displayOff");
        elementB = [null,null,null,null,null];
        lastMove = 0;
        document.querySelector("#pickedUp").innerHTML = "";
        document.querySelector("#pickedUp").classList.remove("rotation90");
    }
    var event = new Event("mousemove");
    document.dispatchEvent(event);    
});