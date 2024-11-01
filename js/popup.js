const popup = document.querySelector(".popup");
const overlay = document.querySelector(".overlay");
// Funzione per ottenere il messaggio dall'URL
function getMessageFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('message');
}
//funzione per ottenere ID
function getIDFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

// Mostra il popup se c'è un messaggio e id nell'URL
const message = getMessageFromUrl();
const id = getIDFromUrl()

if (message) {
    // Esegui controlli sul messaggio per capire quale popup mostrare
    if (message.includes("Username o password errati. Riprova.")) {
        ErrorePopupLog(message);
    } else if (message.includes("Email già registrata")) {
        ErrorePopupReg(message);
    } else {
        apriPopup(message);
    }
}

// Funzione per aprire il popup
function apriPopup(message) {
    popup.innerHTML = `
    <h2>Benvenuto!</h2>
    <p>${message}</p>
    <button class="ok"><span>Ok</span></button>`;
    popup.classList.add("open-popup");
    overlay.classList.add("open");

    // Aggiungi l'evento al pulsante per chiudere il popup
    document.querySelector(".ok").addEventListener("click", chiudiPopup);
}

//gestione errore popup nel login 
function ErrorePopupLog(message) {
    popup.innerHTML = `
    <h2>Errore!</h2>
    <p>${message}</p>
    <button class="ok"><span>Ok</span></button>`;
    popup.classList.add("open-popup")
    overlay.classList.add("open");
    document.querySelector(".ok").addEventListener("click", chiudiPopupErrorLog);
}
//gestione errore popup nel login
function ErrorePopupReg(message) {
    popup.innerHTML = `
    <h2>Errore!</h2>
    <p>${message}</p>
    <button class="ok"><span>Ok</span></button>`;
    popup.classList.add("open-popup")
    overlay.classList.add("open");
    document.querySelector(".ok").addEventListener("click", chiudiPopupErrorReg);
}

// Funzione per chiudere il popup e reindirizzare alla home
function chiudiPopup() {
    overlay.classList.remove("open");
    popup.classList.remove("open-popup");
    window.location.href = "../VPS/home/home.php?id=" + id;; // Reindirizza alla home
}

function chiudiPopupErrorReg() {
    overlay.classList.remove("open");
    popup.classList.remove("open-popup");
    window.location.href = "../VPS/index.html"; // Reindirizza alla registrazione
}

function chiudiPopupErrorLog() {
    overlay.classList.remove("open");
    popup.classList.remove("open-popup");
    window.location.href = "../VPS/login.html"; // Reindirizza al login
}
