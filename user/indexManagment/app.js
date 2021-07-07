function load(whatLoad) {
    var header = document.querySelector("#mainContent h1");
    var nav = document.querySelector("#mainContent nav");
    var content = document.querySelector("#mainContent main");
    switch (whatLoad) {
        case "mainPage":
            header.innerHTML = "Witaj";
            nav.innerHTML = `<div class="selected">Strona główna</div>|<div onclick='load("Logging")'>Logowanie</div>|<div onclick='load("Register")'>Rejestracja</div>`;
            content.innerHTML = `Darmowa gra w Reversi!`;
        break;
        case "Logging":
            header.innerHTML = "Logowanie";
            nav.innerHTML = `<div onclick='load("mainPage")'>Strona główna</div>|<div class="selected">Logowanie</div>|<div onclick='load("Register")'>Rejestracja</div>`;
            content.innerHTML = `
                <form action="user/indexManagment/login.php" method="POST">
                    <section><label for="nick">Nick:</label><input id="nick" name="nick" type="text" required></section><br>
                    <section><label for="password">Podaj hasło:</label><input id="password" name="password" type="password" required></section><br>
                    <button type="submit">Prześlij</button>
                </form>
            `;
        break;
        case "Register":
            header.innerHTML = "Rejestracja";
            nav.innerHTML = `<div onclick='load("mainPage")'>Strona główna</div>|<div onclick='load("Logging")'>Logowanie</div>|<div class="selected">Rejestracja</div>`;
            content.innerHTML = `
                <form action="user/indexManagment/register.php" method="POST">
                    <section><label for="nick" required>Nick:</label><input id="nick" name="nick" type="text" required></section><br>
                    <section><label for="password">Podaj hasło:</label><input id="password" name="password" type="password" required></section><br>
                    <section><label for="password2">Wpisz ponownie hasło:</label><input id="password2" name="password2" type="password" required></section><br>
                    <section><label for="email">(Opcjonalnie) email:</label><input id="email" name="email" type="text"></section><br>
                    <button type="submit">Prześlij</button>
                </form>
            `;
        break;
    }
}