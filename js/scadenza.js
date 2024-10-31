document.addEventListener('DOMContentLoaded', function () {
    const oggi = new Date();
    document.querySelectorAll('tr').forEach(function (riga) {
        const cellaScadenza = riga.querySelector('td:nth-child(4)');
        if (cellaScadenza) {
            const dataScadenza = new Date(cellaScadenza.textContent.trim());

            // Calcola la differenza in giorni
            const differenzaGiorni = Math.floor((oggi - dataScadenza) / (1000 * 60 * 60 * 24));

            if (differenzaGiorni > 15) {
                // Polizza scaduta da più di 15 giorni
                riga.classList.add('fuori-copertura');
            } else if (differenzaGiorni > 8) {
                // Polizza scaduta tra 8 e 15 giorni
                riga.classList.add('in-scadenza');
            }
        }
    });
});
