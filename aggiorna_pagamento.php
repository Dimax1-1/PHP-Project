<?php
// Connessione al database PostgreSQL
$dbhost = "localhost";
$dbport = "5432";
$dbname = "Progetto";
$dbuser = "postgres";
$dbpass = "123456";
$conn = pg_connect("host=$dbhost port=$dbport dbname=$dbname user=$dbuser password=$dbpass");

if (!$conn) {
    echo json_encode(["success" => false, "message" => "Errore nella connessione al database: " . pg_last_error()]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$numero_polizza = pg_escape_string($conn, $data['numero_polizza']);

// Aggiorna lo stato del pagamento
$query_update = "UPDATE Cliente SET pagato = true WHERE numero_polizza = '$numero_polizza'";
$result_update = pg_query($conn, $query_update);

if (!$result_update) {
    echo json_encode(["success" => false, "message" => "Si è verificato un errore durante l'aggiornamento dello stato pagamento: " . pg_last_error($conn)]);
    exit();
}

// Recupera i dati del cliente
$query_select = "SELECT * FROM Cliente WHERE numero_polizza = '$numero_polizza'";
$result_select = pg_query($conn, $query_select);

if ($result_select && pg_num_rows($result_select) > 0) {
    $cliente = pg_fetch_assoc($result_select);

    // Calcola la nuova data di scadenza
    $scadenza = new DateTime($cliente['scadenza']);
    if ($cliente['frazionamento'] == 'A') {
        $scadenza->modify('+1 year');
    } elseif ($cliente['frazionamento'] == 'S') {
        $scadenza->modify('+6 months');
    }
    $nuova_scadenza = $scadenza->format('Y-m-d');

    // Inserisci il nuovo cliente con la scadenza aggiornata
    $query_insert = "INSERT INTO Cliente (nome, numero_polizza, scadenza, premio, fattura, frazionamento, indicizzazione, agenzia, pagato)
                     VALUES ('" . pg_escape_string($conn, $cliente['nome']) . "', 
                             '" . pg_escape_string($conn, $cliente['numero_polizza'] . '_dup') . "', 
                             '$nuova_scadenza', 
                             '" . pg_escape_string($conn, $cliente['premio']) . "', 
                             '" . pg_escape_string($conn, $cliente['fattura']) . "', 
                             '" . pg_escape_string($conn, $cliente['frazionamento']) . "', 
                             " . ($cliente['indicizzazione'] ? 'true' : 'false') . ", 
                             '" . pg_escape_string($conn, $cliente['agenzia']) . "', 
                             false)";
    $result_insert = pg_query($conn, $query_insert);

    if ($result_insert) {
        $_SESSION['duplicato'][$numero_polizza] = pg_escape_string($conn, $cliente['numero_polizza'] . '_dup');
        echo json_encode(["success" => true, "message" => "Pagamento aggiornato e cliente duplicato con successo."]);
    } else {
        echo json_encode(["success" => false, "message" => "Si è verificato un errore durante l'inserimento del nuovo cliente: " . pg_last_error($conn)]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Cliente non trovato."]);
}

pg_close($conn);
?>