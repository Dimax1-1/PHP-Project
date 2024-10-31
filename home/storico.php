<?php
include '../connection.php';
include '../php/traduci.php';

// Recupera l'ID_Utente dall'URL
$ID_Utente = isset($_GET['id']) ? $_GET['id'] : '';

// Controlla se è presente un'azione di eliminazione
if (isset($_GET['elimina_cliente_id'])) {
    $elimina_cliente_id = $_GET['elimina_cliente_id'];
    
    // Esegui la query di eliminazione
    $query_delete = "DELETE FROM clienti WHERE ID = $elimina_cliente_id AND ID_Utente = '$ID_Utente'";
    $result_delete = pg_query($conn, $query_delete);
    
    // Controlla se l'eliminazione è avvenuta con successo
    if ($result_delete) {
        echo "Cliente eliminato con successo!";
    } else {
        echo "Errore nell'eliminazione del cliente: " . pg_last_error($conn);
    }

    // Reindirizza alla pagina senza il parametro elimina_cliente_id per evitare ripetizioni dell'azione
    header("Location: storico.php?id=$ID_Utente");
    exit;
}

// Query per selezionare i clienti
$query_select = "SELECT id, nome, numero_telefono, ramo, numero_polizza, scadenza, premio, indicizzato, agenzia, 
                TO_CHAR(scadenza, 'DD-MM-YYYY') AS scadenza_formattata, DATE_TRUNC('month', scadenza) AS mese_scadenza 
                FROM clienti WHERE ID_Utente = '$ID_Utente' ORDER BY mese_scadenza";
$result_select = pg_query($conn, $query_select);

$clienti_completi = [];
if ($result_select) {
    while ($row = pg_fetch_assoc($result_select)) {
        $cliente_id = $row['id'];
        
        // Estrai le email per questo cliente
        $email_query = "SELECT email FROM emails WHERE cliente_id = $cliente_id";
        $email_result = pg_query($conn, $email_query);
        $emails = [];
        while ($email_row = pg_fetch_assoc($email_result)) {
            $emails[] = $email_row['email'];
        }
        
        // Aggiungi le email al record del cliente
        $row['indirizzo_email'] = implode(", ", $emails); // Unisci le email con una virgola
        
        $mese_scadenza = traduciMese($row['scadenza']);
        $clienti_completi[$mese_scadenza][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Storico Clienti</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="tabella-clienti">
        <h1>Storico Completo dei Clienti</h1>
        <?php if (!empty($clienti_completi)): ?>
            <?php foreach ($clienti_completi as $mese => $clienti): ?>
                <h2><?php echo $mese; ?></h2>
                <table>
                <tr>
                    <th>Nome</th>
                    <th>Numero Telefonico</th>
                    <th>Email</th>
                    <th>Polizza</th>
                    <th>Ramo</th>
                    <th>Scadenza</th>
                    <th>Premio</th>
                    <th>Indicizzato</th>
                    <th>Agenzia</th>
                    <th>Pagato</th>
                    <th>Annulla azione</th>
                    <th>Avvisa</th>
                    <th>Modifica</th>
                    <th>Elimina</th>
                </tr>
                    <?php foreach ($clienti as $cliente): ?>
                        <tr id="cliente-<?php echo $cliente['id']; ?>">
                            <td><?php echo $cliente['nome']; ?></td>
                            <td><?php echo $cliente['numero_telefono']; ?></td>
                            <td><?php echo $cliente['indirizzo_email']; ?></td>
                            <td><?php echo $cliente['numero_polizza']; ?></td>
                            <td><?php echo $cliente['ramo']; ?></td>
                            <td><?php echo $cliente['scadenza_formattata']; ?></td>
                            <td><?php echo $cliente['premio']; ?></td>
                            <td><?php echo $cliente['indicizzato'] === 't' ? 'Si' : 'No'; ?></td>
                            <td><?php echo $cliente['agenzia']; ?></td>
                            <td><button class="pagato-btn" onclick="segnaPagato(<?php echo $cliente['id']; ?>)">Pagato</button></td>
                            <td><button class="annulla-btn" onclick="annullaAzione(<?php echo $cliente['id']; ?>)">Annulla</button></td>
                            <td><button class="avvisa-btn" onclick="avvisaCliente(<?php echo $cliente['id']; ?>)">Avvisa</button></td>
                            <td><button class="modifica-btn" onclick="modificaCliente(<?php echo $cliente['id']; ?>)">Modifica</button></td>
                            <td><a href="storico.php?id=<?php echo $ID_Utente; ?>&elimina_cliente_id=<?php echo $cliente['id']; ?>"
                                    onclick="return confirm('Sei sicuro di voler eliminare questo cliente?');">Elimina</a></td>
                        </tr>
                        <tr id="modifica-form-<?php echo $cliente['id']; ?>" style="display: none;">
                            <form action="../php/modifica_cliente.php?id=<?php echo $ID_Utente; ?>" method="POST">
                                <td><input type="text" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>"></td>
                                <td><input type="text" name="numero_telefono"
                                        value="<?php echo htmlspecialchars($cliente['numero_telefono']); ?>"></td>
                                <td><input type="text" name="indirizzo_email"
                                        value="<?php echo htmlspecialchars($cliente['indirizzo_email']); ?>"></td>
                                <td><input type="text" name="numero_polizza"
                                        value="<?php echo htmlspecialchars($cliente['numero_polizza']); ?>"></td>
                                <td><input type="text" name="ramo" value="<?php echo htmlspecialchars($cliente['ramo']); ?>"></td>
                                <td><input type="date" name="scadenza" value="<?php echo htmlspecialchars($cliente['scadenza']); ?>"></td>
                                <td><input type="text" name="premio" value="<?php echo htmlspecialchars($cliente['premio']); ?>"></td>
                                <td><input type="text" name="agenzia" value="<?php echo htmlspecialchars($cliente['agenzia']); ?>"></td>
                                <td colspan="3">
                                    <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                                    <button type="submit">Salva Modifiche</button>
                                    <button type="button" onclick="annullaModifica(<?php echo $cliente['id']; ?>)">Annulla</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </table>

            <?php endforeach; ?>
        <?php else: ?>
            <p>Nessun cliente trovato.</p>
        <?php endif; ?>
    </div>

    <div class="form-ritorna-home">
        <form action="home.php" method="GET">
            <a href="home.php?reset=true&id=<?php echo htmlspecialchars($ID_Utente); ?>">Torna alla pagina di inserimento</a>
        </form>
    </div>
</body>
<script src="../js/clienti.js"></script>
<script src="../js/avvisa.js"></script>
<script src="../js/scadenza.js"></script>

</html>
