function creaNuovaTabella() {
    var tabellaEsistente = document.querySelector('.content-table');

    var nuovaTabella = tabellaEsistente.cloneNode(true);
    var righe = nuovaTabella.getElementsByTagName('tr');

    for (var i = 0; i < righe.length; i++) {
        var celle = righe[i].getElementsByTagName('td');

        for (var j = 0; j < celle.length - 3; j++) {
            var cella = celle[j];
            cella.innerHTML = '<input type="text" value="' + cella.innerText + '">';
        }

        // Rimuovi le ultime tre celle (colonne)
        for (var k = 0; k < 3; k++) {
            if (celle.length > 0) {
                celle[celle.length - 1].remove();
            }
        }
    }

    // Rimuovi le intestazioni delle ultime tre colonne
    var intestazioni = nuovaTabella.getElementsByTagName('th');
    for (var l = 0; l < 3; l++) {
        if (intestazioni.length > 0) {
            intestazioni[intestazioni.length - 1].remove();
        }
    }

    tabellaEsistente.parentNode.insertBefore(nuovaTabella, tabellaEsistente);
}

function annullaModifiche() {
    window.location.href = 'connactio.php';
}

function salvaModifiche() {
    var tabellaModificabile = document.querySelector('.content-table');
    var righe = tabellaModificabile.getElementsByTagName('tr');

    var datiDaSalvare = [];

    for (var i = 1; i < righe.length; i++) { // Inizia da 1 per saltare l'intestazione
        var celle = righe[i].getElementsByTagName('td');
        var datiRiga = [];

        for (var j = 0; j < celle.length; j++) {
            var cella = celle[j];
            var valore = cella.querySelector('input').value;
            datiRiga.push(valore);
        }

        datiDaSalvare.push(datiRiga);
    }

    // Invia i dati al server utilizzando AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'salva_modifiche.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
                // Aggiorna la pagina dopo il salvataggio completato
                location.reload();
            } else {
                console.error('Si è verificato un errore durante il salvataggio.');
            }
        }
    };
    xhr.send(JSON.stringify(datiDaSalvare));
}



function annullaModifiche() {
    window.location.href = 'connactio.php';
}

function salvaModifiche() {
    var tabellaModificabile = document.querySelector('.content-table');
    var righe = tabellaModificabile.getElementsByTagName('tr');

    var datiDaSalvare = [];

    for (var i = 1; i < righe.length; i++) { // Inizia da 1 per saltare l'intestazione
        var celle = righe[i].getElementsByTagName('td');
        var datiRiga = [];

        for (var j = 0; j < celle.length; j++) {
            var cella = celle[j];
            var input = cella.querySelector('input') || cella.querySelector('select');
            var valore = input ? input.value : cella.innerText;

            // Per la colonna indicizzazione, converti il valore in booleano
            if (j === 6) { // La colonna indicizzazione è la sesta colonna (indice 6)
                valore = (valore === 'true' || valore === 'Si') ? 'true' : 'false';
            }

            datiRiga.push(valore);
        }

        datiDaSalvare.push(datiRiga);
    }

    // Invia i dati al server utilizzando AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'salva_modifiche.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
                // Aggiorna la pagina dopo il salvataggio completato
                location.reload();
            } else {
                console.error('Si è verificato un errore durante il salvataggio.');
            }
        }
    };
    xhr.send(JSON.stringify(datiDaSalvare));
}
