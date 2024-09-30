<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="logo/logo.svg">
    <title>Gestione Clienti</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <h1>Inserimento Dati Clienti</h1>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="inserimento">
        <label for="nome">Cliente:</label>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="indirizzo">Indirizzo Email:</label>
        <input type="text" id="indirizzo" name="indirizzo" required><br><br>

        <label for="numero_polizza">Numero Polizza/Targa:</label>
        <input type="text" id="numero_polizza" name="numero_polizza" required><br><br>

        <label for="scadenza">Scadenza:</label>
        <input type="date" id="scadenza" name="scadenza" required><br><br>

        <label for="premio">Premio:</label>
        <input type="text" id="premio" name="premio" required><br><br>

        <label for="fattura">Fattura:</label>
        <input type="text" id="fattura" name="fattura"><br><br>

        <label for="frazionamento">Frazionamento:</label>
        <select id="frazionamento" name="frazionamento" required>
            <option value="A">Annuale</option>
            <option value="S">Semestrale</option>
        </select><br><br>

        <label for="indicizzato">Indicizzazione:</label>
        <select id="indicizzato" name="indicizzato">
            <option value="true">Si</option>
            <option value="false">No</option>
        </select><br><br>

        <label for="agenzia">Agenzia:</label>
        <input type="text" id="agenzia" name="agenzia" required><br><br>

        <input type="submit" value="Inserisci">
    </form>

    <div class="action-buttons">
        <button onclick="creaNuovaTabella()">Modifica</button>
        <button onclick="salvaModifiche()">Salva Modifiche</button>
        <button onclick="annullaModifiche()">Annulla modifiche</button>
    </div>

    <h2>Seleziona il Mese da Visualizzare</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
        <label for="mese">Mese:</label>
        <select name="mese" id="mese">
            <option value="0" <?php if (isset($_GET['mese']) && $_GET['mese'] == 0)
                echo 'selected'; ?>>Nessuno</option>
            <option value="1" <?php if (isset($_GET['mese']) && $_GET['mese'] == 1)
                echo 'selected'; ?>>Gennaio</option>
            <option value="2" <?php if (isset($_GET['mese']) && $_GET['mese'] == 2)
                echo 'selected'; ?>>Febbraio</option>
            <option value="3" <?php if (isset($_GET['mese']) && $_GET['mese'] == 3)
                echo 'selected'; ?>>Marzo</option>
            <option value="4" <?php if (isset($_GET['mese']) && $_GET['mese'] == 4)
                echo 'selected'; ?>>Aprile</option>
            <option value="5" <?php if (isset($_GET['mese']) && $_GET['mese'] == 5)
                echo 'selected'; ?>>Maggio</option>
            <option value="6" <?php if (isset($_GET['mese']) && $_GET['mese'] == 6)
                echo 'selected'; ?>>Giugno</option>
            <option value="7" <?php if (isset($_GET['mese']) && $_GET['mese'] == 7)
                echo 'selected'; ?>>Luglio</option>
            <option value="8" <?php if (isset($_GET['mese']) && $_GET['mese'] == 8)
                echo 'selected'; ?>>Agosto</option>
            <option value="9" <?php if (isset($_GET['mese']) && $_GET['mese'] == 9)
                echo 'selected'; ?>>Settembre</option>
            <option value="10" <?php if (isset($_GET['mese']) && $_GET['mese'] == 10)
                echo 'selected'; ?>>Ottobre</option>
            <option value="11" <?php if (isset($_GET['mese']) && $_GET['mese'] == 11)
                echo 'selected'; ?>>Novembre
            </option>
            <option value="12" <?php if (isset($_GET['mese']) && $_GET['mese'] == 12)
                echo 'selected'; ?>>Dicembre
            </option>
        </select>
        <label for="anno">Anno:</label>
        <select name="anno" id="anno">
            <?php
            $anno_corrente = date("Y");
            for ($anno = $anno_corrente + 1; $anno >= $anno_corrente - 10; $anno--) {
                $selected = ($anno == $anno_corrente) ? 'selected' : '';
                echo "<option value='$anno' $selected>$anno</option>";
            }
            ?>
        </select>
        <input type="submit" value="Visualizza">
    </form>

    <h2>Elenco Clienti</h2>

    <?php
    session_start();

    // Connessione al database PostgreSQL
    $dbhost = "localhost";
    $dbport = "5432";
    $dbname = "Progetto";
    $dbuser = "postgres";
    $dbpass = "123456";
    $conn = pg_connect("host=$dbhost port=$dbport dbname=$dbname user=$dbuser password=$dbpass");

    if (!$conn) {
        echo "Errore nella connessione al database: " . pg_last_error();
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_POST['elimina']) && !isset($_POST['paga']) && !isset($_POST['annulla'])) {
            $nome = $_POST['nome'];
            $numero_polizza = $_POST['numero_polizza'];
            $scadenza = $_POST['scadenza'];
            $premio = $_POST['premio'];
            $frazionamento = $_POST['frazionamento'];
            $indicizzazione = $_POST['indicizzato'] === 'true' ? 'true' : 'false';
            $agenzia = $_POST['agenzia'];
            $fattura = $_POST['fattura'];

            $query = "INSERT INTO Cliente (nome, numero_polizza, scadenza, premio, fattura, frazionamento, indicizzazione, agenzia, pagato) 
              VALUES ('$nome', '$numero_polizza', '$scadenza', '$premio', '$fattura', '$frazionamento', $indicizzazione, '$agenzia', false)";
            $result = pg_query($conn, $query);

            if ($result) {
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                echo "Si è verificato un errore durante l'inserimento dei dati: " . pg_last_error($conn);
            }
        } elseif (isset($_POST['elimina']) && $_POST['elimina'] == 'Elimina') {
            $numero_polizza_da_elim = $_POST['numero_polizza'];
            $query_elimina = "DELETE FROM Cliente WHERE numero_polizza = '$numero_polizza_da_elim'";
            $result_elimina = pg_query($conn, $query_elimina);

            if ($result_elimina) {
                header("Refresh:0; url={$_SERVER['PHP_SELF']}?t=" . time());
            } else {
                echo "Si è verificato un errore durante l'eliminazione del cliente: " . pg_last_error($conn);
            }
        } elseif (isset($_POST['numero_polizza']) && isset($_POST['paga'])) {
            $numero_polizza = $_POST['numero_polizza'];

            $query_update = "UPDATE Cliente SET pagato = true WHERE numero_polizza = '$numero_polizza'";
            $result_update = pg_query($conn, $query_update);

            if (!$result_update) {
                echo "Si è verificato un errore durante l'aggiornamento dello stato pagamento: " . pg_last_error($conn);
            } else {
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

                    // Crea un numero di polizza duplicato unico
                    $numero_polizza_duplicato = $numero_polizza . '_' . uniqid();

                    // Inserisci il nuovo cliente con la scadenza aggiornata
                    $query_insert = "INSERT INTO Cliente (nome, numero_polizza, scadenza, premio, fattura, frazionamento, indicizzazione, agenzia, pagato)
                                 VALUES ('" . pg_escape_string($conn, $cliente['nome']) . "', 
                                         '$numero_polizza_duplicato', 
                                         '$nuova_scadenza', 
                                         '" . pg_escape_string($conn, $cliente['premio']) . "', 
                                         '" . pg_escape_string($conn, $cliente['fattura']) . "', 
                                         '" . pg_escape_string($conn, $cliente['frazionamento']) . "', 
                                         '" . pg_escape_string($conn, $cliente['indicizzazione']) . "', 
                                         '" . pg_escape_string($conn, $cliente['agenzia']) . "', 
                                         false)";
                    $result_insert = pg_query($conn, $query_insert);

                    if ($result_insert) {
                        $_SESSION['duplicato'][$numero_polizza] = $numero_polizza_duplicato;
                    } else {
                        echo "Si è verificato un errore durante l'inserimento del nuovo cliente: " . pg_last_error($conn);
                    }
                } else {
                    echo "Cliente non trovato.";
                }
            }
        } elseif (isset($_POST['numero_polizza']) && isset($_POST['annulla'])) {
            $numero_polizza = $_POST['numero_polizza'];

            $query_annulla = "UPDATE Cliente SET pagato = false WHERE numero_polizza = '$numero_polizza'";
            $result_annulla = pg_query($conn, $query_annulla);

            if ($result_annulla) {
                // Elimina il cliente duplicato
                if (isset($_SESSION['duplicato'][$numero_polizza])) {
                    $numero_polizza_duplicato = $_SESSION['duplicato'][$numero_polizza];
                    $query_elimina_duplicato = "DELETE FROM Cliente WHERE numero_polizza = '$numero_polizza_duplicato'";
                    $result_elimina_duplicato = pg_query($conn, $query_elimina_duplicato);

                    if (!$result_elimina_duplicato) {
                        echo "Si è verificato un errore durante l'eliminazione del cliente duplicato: " . pg_last_error($conn);
                    } else {
                        unset($_SESSION['duplicato'][$numero_polizza]);
                    }
                }
                header("Refresh:0; url={$_SERVER['PHP_SELF']}?t=" . time());
            } else {
                echo "Si è verificato un errore durante l'annullamento dello stato pagamento: " . pg_last_error($conn);
            }
        }
    }

    $query_select = "SELECT * FROM Cliente ORDER BY EXTRACT(MONTH FROM scadenza), nome";

    if (isset($_GET['mese']) && isset($_GET['anno']) && $_GET['mese'] != 0) {
        $mese_selezionato = $_GET['mese'];
        $anno_selezionato = $_GET['anno'];
        $data_inizio = date('Y-m-01', mktime(0, 0, 0, $mese_selezionato, 1, $anno_selezionato));
        $data_fine = date('Y-m-t', mktime(0, 0, 0, $mese_selezionato, 1, $anno_selezionato));
        $query_select = "SELECT * FROM Cliente WHERE EXTRACT(MONTH FROM scadenza) = $mese_selezionato AND EXTRACT(YEAR FROM scadenza) = $anno_selezionato ORDER BY scadenza, nome";
    } else {
        $query_select = "SELECT * FROM Cliente ORDER BY scadenza, nome";
    }
    $result_select = pg_query($conn, $query_select);

    if (!$result_select) {
        echo "Si è verificato un errore durante il recupero dei dati dalla tabella: " . pg_last_error($conn);
    } else {
        $num_rows = pg_num_rows($result_select);

        if ($num_rows > 0) {
            echo "<table class='content-table'>
    <thead>
    <tr>
    <th>Nome</th>
    <th>Numero Polizza</th>
    <th>Scadenza</th>
    <th>Premio</th>
    <th>Fattura</th>
    <th>Frazionamento</th>
    <th>Indicizzazione</th>
    <th>Agenzia</th>
    <th>Stato Polizza</th>
    <th>Pagato</th>
    <th>Annulla azione</th>
    <th>Elimina</th>
    </tr>
    </thead>";

            while ($row = pg_fetch_assoc($result_select)) {
                // Rimuove il prefisso aggiunto al numero polizza duplicato
                $numero_polizza_display = explode('_', $row['numero_polizza'])[0];

                echo "<tr id='row_" . $numero_polizza_display . "' class='" . ($row['pagato'] == 't' ? 'attiva' : '') . "'>";
                echo "<td>" . $row['nome'] . "</td>";
                echo "<td>" . $numero_polizza_display . "</td>";
                echo "<td>" . date('d/m/Y', strtotime($row['scadenza'])) . "</td>";
                echo "<td>" . $row['premio'] . "</td>";
                echo "<td>" . $row['fattura'] . "</td>";
                echo "<td>" . $row['frazionamento'] . "</td>";
                echo "<td>" . ($row['indicizzazione'] === 'true' ? 'Si' : 'No') . "</td>";
                echo "<td>" . $row['agenzia'] . "</td>";

                echo "<td id='payment_status_" . $numero_polizza_display . "'>";
                if ($row['pagato'] == 't') {
                    echo "Pagato";
                } else {
                    echo "Non Pagato";
                }
                echo "</td>";

                echo "<td>
        <form action='' method='POST' class='pagato' onsubmit='return segnaComePagato(event, \"" . $row['numero_polizza'] . "\")'>
        <input type='hidden' name='numero_polizza' value='" . $row['numero_polizza'] . "'>
        <input type='hidden' name='paga'>
        <input type='submit' value='Pagato'>
        </form>
        </td>";

                echo "<td>
        <form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='POST' class ='annulla'>
        <input type='hidden' name='numero_polizza' value='" . $row['numero_polizza'] . "'>
        <input type='hidden' name='annulla'>
        <input type='hidden' name='numero_polizza_duplicato' value='" . (isset($_SESSION['duplicato'][$row['numero_polizza']]) ? $_SESSION['duplicato'][$row['numero_polizza']] : '') . "'>
        <input type='submit' value='Annulla'>
        </form>
        </td>";
                echo "<td>
        <form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='POST' class ='elimina'>
        <input type='hidden' name='numero_polizza' value='" . $row['numero_polizza'] . "'>
        <input type='submit' name='elimina' value='Elimina'>
        </form>
        </td>";

                echo "</tr>";
            }
        } else {
            echo "Nessun cliente trovato.";
        }
        echo "</table>";
    }

    pg_close($conn);
    ?>


    <script src="modifica.js"></script>
    <script src="scandenza.js"></script>
    <script src="pagato.js"></script>
</body>

</html>