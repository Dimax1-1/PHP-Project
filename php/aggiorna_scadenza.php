<?php
include '../connection.php';
$ID_Utente = isset($_GET['id']) ? $_GET['id'] : '';
if (isset($_POST['cliente_id'])) {
    $cliente_id = $_POST['cliente_id'];
    
    //recupera il frazionamento atturale e la scadenza attuale
    $query = "SELECT scadenza, frazionamento FROM clienti WHERE id = $cliente_id";
    $result = pg_query($conn, $query);
    $cliente = pg_fetch_assoc($result);
    
    if ($cliente) {
        $scadenza_attuale = new DateTime($cliente['scadenza']);
        $frazionamento = $cliente['frazionamento'];
        
        if ($frazionamento === 'Annuale') {
            $scadenza_attuale -> modify('+1 year');
        } elseif ($frazionamento === 'Semestrale') {
            $scadenza_attuale->modify('+6 months');
        }
        
        $nuova_scadenza = $scadenza_attuale -> format('Y-m-d');
        //Inserisce una copia dei dati cambiando la scadenza aggiorna del record
        $query_inserisci = "INSERT INTO clienti (nome, numero_telefono, numero_polizza, ramo, scadenza, premio, fattura, frazionamento, indicizzato, agenzia, id_utente)
                    SELECT nome, numero_telefono, numero_polizza, ramo, '$nuova_scadenza', premio, fattura, frazionamento, indicizzato, agenzia, '$ID_Utente'
                    FROM clienti WHERE id = $cliente_id RETURNING id";
        $result_inserisci = pg_query($conn, $query_inserisci);

        if ($result_inserisci) {
            // Ottieni l'ID del nuovo cliente
            $nuovo_cliente_id = pg_fetch_result($result_inserisci, 0, 'id');

            // Inserisci le email associate al cliente originale con il nuovo cliente_id
            $query_copia_email = "INSERT INTO emails (email, cliente_id)
                          SELECT email, $nuovo_cliente_id
                          FROM emails WHERE cliente_id = $cliente_id";
            pg_query($conn, $query_copia_email);
        } else {
            echo "Errore durante l'inserimento del cliente e delle email.";
        }
    } else {
        echo "Cliente non trovato.";
    }

}
?>