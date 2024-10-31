<?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_id = $_POST['cliente_id'];
    $nome = $_POST['nome'];
    $email = $_POST['indirizzo_email'];
    $polizza = $_POST['numero_polizza'];
    $scadenza = $_POST['scadenza'];
    $premio = $_POST['premio'];
    $agenzia = $_POST['agenzia'];
    $ID_Utente = $_POST['ID_Utente'];

    // Aggiorna i dati del cliente nel database
    $query_update = "UPDATE clienti SET 
                        nome = '$nome', 
                        numero_polizza = '$polizza', 
                        scadenza = '$scadenza', 
                        premio = '$premio', 
                        agenzia = '$agenzia' 
                     WHERE id = $cliente_id";
    $result_update = pg_query($conn, $query_update);

    if ($result_update) {
        // Aggiorna anche le email
        $emails = explode(",", $email);
        pg_query($conn, "DELETE FROM emails WHERE cliente_id = $cliente_id"); // Prima rimuovi le vecchie email
        foreach ($emails as $email) {
            $email = trim(pg_escape_string($conn, $email));
            pg_query($conn, "INSERT INTO emails (email, cliente_id) VALUES ('$email', $cliente_id)");
        }

        // Reindirizza dopo il salvataggio
        header("Location: ../home/storico.php?id=" . $_GET['id']);
        exit;
    } else {
        echo "Errore nell'aggiornamento del cliente: " . pg_last_error($conn);
    }
}
?>
