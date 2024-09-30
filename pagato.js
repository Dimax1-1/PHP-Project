function segnaComePagato(event, numeroPolizza) {
    event.preventDefault();

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'aggiorna_pagamento.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        console.log(response.message);
                        var row = document.getElementById('row_' + numeroPolizza);
                        var paymentStatus = document.getElementById('payment_status_' + numeroPolizza);
                        if (row && paymentStatus) {
                            row.classList.add('attiva');
                            paymentStatus.innerText = 'Pagato';
                        }
                        location.reload();
                    } else {
                        console.error(response.message);
                    }
                } catch (e) {
                    console.error("Errore nel parsing della risposta JSON: ", e);
                }
            } else {
                console.error("Si è verificato un errore durante l'aggiornamento del pagamento.");
            }
        }
    };
    xhr.send(JSON.stringify({ numero_polizza: numeroPolizza }));
}
