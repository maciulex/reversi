function changeMotive() {
    let mov = window.motiveAccess;
    const d = new Date();
    d.setTime(d.getTime() + 5356800000);
    let expires = "expires="+ d.toUTCString();
    if (mov == undefined || (mov != 0 && mov != 1)) {
        mov = 1;
        window.motiveAccess = 1;
    } 
    switch (mov) {
        case 0:
            document.cookie = "motive=1;"+expires+";path=/";
            window.motiveAccess = 1;
        break;
        case 1:
            document.cookie = "motive=0;"+expires+";path=/";
            window.motiveAccess = 0;
        break;
    }
    setMotive();
}
function setMotive() {
    let buttonPlace = document.querySelector(".changeMotive");
    let pathName = "/"+(window.location.pathname.split("/")[1])+"/";
    switch (window.motiveAccess) { 
        case 0:
            buttonPlace.innerHTML = `<img src="${pathName}photos/ico/moon-solid.svg">`;
            document.querySelector("body").classList.add("dayMotive");
            document.querySelector("body").classList.remove("nightMotive");
        break;
        case 1:
            buttonPlace.innerHTML = `<img src="${pathName}photos/ico/sun-solid.svg">`;    
            document.querySelector("body").classList.add("nightMotive");
            document.querySelector("body").classList.remove("dayMotive");
        break;
    }
}