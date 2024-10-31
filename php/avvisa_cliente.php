<?php
include '../connection.php';

if (isset($_POST['cliente_id'])) {
    $cliente_id = $_POST['cliente_id'];

    // Inserisce la data odierna come avviso nella tabella avvisi
    $query = "INSERT INTO avvisi (cliente_id, data_avviso) VALUES ($cliente_id, NOW())";
    $result = pg_query($conn, $query);

    if ($result) {
        echo "Avviso registrato con successo.";
    } else {
        echo "Errore nella registrazione dell'avviso: " . pg_last_error($conn);
    }
}
?>
