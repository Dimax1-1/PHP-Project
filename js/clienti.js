function getIDFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}
const id = getIDFromUrl()
// Funzione per gestire il pulsante "Pagato"
function segnaPagato(clienteId) {
    const row = document.getElementById('cliente-' + clienteId);
    if (row) {
        row.className = 'pagato';//elimina tutte le altre classi e mette solo pagato
        /*copia aggiornamento trasferimento clienti*/
        // Invoca la funzione PHP per aggiornare il database
        const formData = new FormData();
        formData.append('cliente_id', clienteId);
     
        fetch('../php/aggiorna_scadenza.php?id=' + id, {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                alert(data); // Mostra il messaggio di successo o errore
            })
            .catch(error => {
                console.error('Errore:', error);
            });
    }
    /*
    if (row.className == 'in-scadenza') {
        row.classList.remove('in-scadenza') si usa == per confronto === verifica + precisa
    }*/

    /*l'alternativa più "verbosa" è
    if (row.classList.contains('in-scadenza')) {
        row.classList.remove('in-scadenza'); 
    }*/

}

// Funzione per gestire il pulsante "Annulla azione"
function annullaAzione(clienteId) {
    const row = document.getElementById('cliente-' + clienteId);
    if (row) {
        row.classList.remove('pagato');
    }
}

// Mostra il form di modifica
function modificaCliente(clienteId) {
    document.getElementById('cliente-' + clienteId).style.display = 'none'; // Nascondi la riga cliente
    document.getElementById('modifica-form-' + clienteId).style.display = 'table-row'; // Mostra il form di modifica
}

// Annulla la modifica e torna alla visualizzazione normale
function annullaModifica(clienteId) {
    document.getElementById('modifica-form-' + clienteId).style.display = 'none'; // Nascondi il form di modifica
    document.getElementById('cliente-' + clienteId).style.display = 'table-row'; // Mostra di nuovo i dati originali
}
