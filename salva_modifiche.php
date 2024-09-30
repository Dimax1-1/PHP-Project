<?php
// Connessione al database PostgreSQL
$dbhost = "localhost";
$dbport = "5432";
$dbname = "Progetto";
$dbuser = "postgres";
$dbpass = "123456";
$conn = pg_connect("host=$dbhost port=$dbport dbname=$dbname user=$dbuser password=$dbpass");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datiDaSalvare = json_decode(file_get_contents('php://input'), true);

    foreach ($datiDaSalvare as $datiRiga) {
        $numero_polizza = $datiRiga[1];
        $nuovo_nome = $datiRiga[0];
        $nuova_scadenza = $datiRiga[2];
        $nuovo_premio = $datiRiga[3];
        $nuova_fattura = $datiRiga[4];
        $nuovo_frazionamento = $datiRiga[5];
        $nuova_indicizzazione = $datiRiga[6] === 'true' ? 'true' : 'false';
        $nuova_agenzia = $datiRiga[7];

        $query_aggiorna = "UPDATE Cliente SET nome = '$nuovo_nome', scadenza ='$nuova_scadenza', premio = '$nuovo_premio', 
                           fattura = '$nuova_fattura', frazionamento = '$nuovo_frazionamento', indicizzazione = $nuova_indicizzazione, agenzia = '$nuova_agenzia' 
                           WHERE numero_polizza = '$numero_polizza'";
        $result_aggiorna = pg_query($conn, $query_aggiorna);

        if (!$result_aggiorna) {
            echo "Si è verificato un errore durante l'aggiornamento dei dati.";
            exit();
        }
    }

    echo "Salvataggio completato con successo.";
} else {
    echo "Metodo di richiesta non valido.";
}
?>