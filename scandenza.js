// Funzione per aggiungere la classe "Pagata" quando viene premuto il pulsante "Pagato"
function segnaComePagato(event, numero_polizza) {


    // Rimuovi la classe "scaduta" dalla riga specifica del numero di polizza
    var row = document.getElementById("row_" + numero_polizza);
    if (row.classList == "scaduta") {
        row.classList.remove("scaduta");
    } else if (row.classList == "quasi_scaduta") {
        row.classList.remove("quasi_scaduta");
    } else if (row.classList == "senza_cop") {
        row.classList.remove("senza_cop")
    }

    // Aggiorna il testo dello stato pagamento nella riga specifica
    var paymentStatusCell = document.getElementById("payment_status_" + numero_polizza);
    paymentStatusCell.textContent = "Pagato";

    // Controlla se la riga deve rimanere "attiva" o meno
    if (!row.classList.contains("attiva")) {
        // Rimuovi la classe "attiva" solo se non è già presente
        row.classList.remove("attiva");
    }

    // Controlla nuovamente la scadenza dopo aver segnato come pagato
    controllaScadenza();
}


// Funzione per controllare la data di scadenza e aggiungere la classe "Scaduta"
function controllaScadenza() {
    var today = new Date();
    var rows = document.querySelectorAll(".content-table tbody tr");
    rows.forEach(function (row) {
        // Verifica se la riga non ha già la classe "attiva"
        if (!row.classList.contains("attiva")) {
            var scadenzaCell = row.cells[2]; // Assume che la data di scadenza sia nella terza colonna
            var scadenza = new Date(scadenzaCell.textContent);
            var diffGiorni = Math.ceil((scadenza - today) / (1000 * 60 * 60 * 24));
            if (diffGiorni < 0 && Math.abs(diffGiorni) <= 15) {
                row.classList.add("scaduta");
            } else if (diffGiorni < 0 && Math.abs(diffGiorni) <= 7) {
                row.classList.add("quasi_scaduta");
            } else if ((today - scadenza) >= 15) {
                row.classList.add("senza_cop");
            }
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // Chiamata alla funzione controllaScadenza dopo che la pagina è stata completamente caricata
    controllaScadenza();
});