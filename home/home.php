<?php
// Avvia una sessione
session_start();

// Collegamento al database
include '../connection.php';

$message = '';
$ID_Utente = isset($_GET['id']) ? $_GET['id'] : '';

// Inizializza l'array di sessione se non è già presente
if (!isset($_SESSION['clienti_inseriti'])) {
    $_SESSION['clienti_inseriti'] = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ID_Utente = $_POST['user_id'];
    $nome = pg_escape_string($conn, $_POST['nome']);  // Esegui l'escape delle variabili per sicurezza
    $emailArray = isset($_POST['email']) ? $_POST['email'] : [];
    $telefono = pg_escape_string($conn, $_POST['telefono']);
    $polizza = pg_escape_string($conn, $_POST['polizza']);
    $ramo = pg_escape_string($conn, $_POST['ramo']);
    $scadenza = pg_escape_string($conn, $_POST['scadenza']);
    $premio = pg_escape_string($conn, $_POST['premio']);
    $fattura = pg_escape_string($conn, $_POST['fattura']);
    $frazionamento = pg_escape_string($conn, $_POST['frazionamento']);
    $indicizzato = $_POST['indicizzato'] === 'true' ? 'true' : 'false';
    $agenzia = pg_escape_string($conn, $_POST['agenzia']);

    if (!empty($ID_Utente)) {
        // Query per inserire il cliente e restituire l'ID
        $query = "INSERT INTO clienti (nome, numero_telefono, numero_polizza, ramo, scadenza, premio, fattura, frazionamento, indicizzato, agenzia, ID_Utente) 
                  VALUES ('$nome', '$telefono', '$polizza', '$ramo', '$scadenza', '$premio', '$fattura', '$frazionamento', '$indicizzato', '$agenzia', '$ID_Utente') 
                  RETURNING id";

        $result = pg_query($conn, $query);

        if ($result) {
            $message = "Cliente inserito correttamente!";
            $cliente_id = pg_fetch_result($result, 0, 'id'); // Ottieni l'ID del cliente appena inserito

            // Se l'utente ha fornito email, inseriscile nella tabella emails
            if (!empty($emailArray)) {
                foreach ($emailArray as $email) {
                    $email = pg_escape_string($conn, trim($email));
                    $email_query = "INSERT INTO emails (email, cliente_id) VALUES ('$email', $cliente_id)";
                    pg_query($conn, $email_query);
                }
            }

            // Aggiungi il cliente alla sessione per visualizzarlo
            $_SESSION['clienti_inseriti'][] = [
                'nome' => $nome,
                'email' => implode(", ", $emailArray),
                'telefono' => $telefono,
                'polizza' => $polizza,
                'ramo' => $ramo,
                'scadenza' => $scadenza,
                'premio' => $premio,
                'fattura' => $fattura,
                'frazionamento' => $frazionamento,
                'indicizzato' => $indicizzato,
                'agenzia' => $agenzia
            ];
        } else {
            $message = "Errore nell'inserimento del cliente.";
        }
    } else {
        $message = "Errore: ID utente mancante.";
    }
}

// Funzione per resettare i clienti inseriti quando si ritorna indietro
if (isset($_GET['reset'])) {
    $_SESSION['clienti_inseriti'] = [];
}
?>


<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Gestione Clienti</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="inserimento-clienti-container">
        <h1>Inserimento Dati Clienti</h1>
        <form action="home.php" method="POST" class="inserimento">
            <input type="hidden" id="user_id" name="user_id" value="<?php echo htmlspecialchars($ID_Utente); ?>">

            <div class="form-group">
                <label for="nome">Nome Cliente:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <div id="email-container">
                    <input type="email" name="email[]" required>
                </div>
                <button type="button" onclick="addEmailField()">Aggiungi un'altra email</button>
            </div>

            <div class="form-group">
                <label for="telefono">Numero Telefonico:</label>
                <input type="text" id="telefono" name="telefono" required>
            </div>

            <div class="form-group">
                <label for="polizza">Numero Polizza/Targa:</label>
                <input type="text" id="polizza" name="polizza" required>
            </div>

            <div class="form-group">
                <label for="ramo">Ramo:</label>
                <input type="text" id="ramo" name="ramo" required>
            </div>

            <div class="form-group">
                <label for="scadenza">Scadenza:</label>
                <input type="date" id="scadenza" name="scadenza" required>
            </div>

            <div class="form-group">
                <label for="premio">Premio:</label>
                <input type="text" id="premio" name="premio" required>
            </div>

            <div class="form-group">
                <label for="fattura">Fattura:</label>
                <input type="text" id="fattura" name="fattura">
            </div>

            <div class="form-group">
                <label for="frazionamento">Frazionamento:</label>
                <select id="frazionamento" name="frazionamento" required>
                    <option value="Annuale">Annuale</option>
                    <option value="Semestrale">Semestrale</option>
                </select>
            </div>

            <div class="form-group">
                <label for="indicizzato">Indicizzato:</label>
                <select id="indicizzato" name="indicizzato">
                    <option value="true">Sì</option>
                    <option value="false">No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="agenzia">Agenzia:</label>
                <input type="text" id="agenzia" name="agenzia" required>
            </div>

            <button type="submit">Inserisci Cliente</button>
        </form>

        <!-- Mostra il messaggio di successo -->
        <?php if (!empty($message)): ?>
            <p class="message">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Pulsante per vedere lo storico e resettare la sessione -->
    <div class="form-visualizza-storico">
        <form action="storico.php" method="GET">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($ID_Utente); ?>">
            <button type="submit">Visualizza Storico Completo</button>
        </form>
    </div>

    <!-- Mostra la tabella con i clienti inseriti solo in questa sessione -->
    <?php if (!empty($_SESSION['clienti_inseriti'])): ?>
        <div class="tabella-clienti">
            <h2>Clienti inseriti:</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th>Numero Polizza</th>
                        <th>Ramo</th>
                        <th>Scadenza</th>
                        <th>Premio</th>
                        <th>Fattura</th>
                        <th>Frazionamento</th>
                        <th>Indicizzato</th>
                        <th>Agenzia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['clienti_inseriti'] as $cliente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['polizza']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['ramo']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['scadenza']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['premio']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['fattura']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['frazionamento']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['indicizzato']=== 'true' ? 'Si' : 'No') ; ?></td>
                            <td><?php echo htmlspecialchars($cliente['agenzia']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <script src="../js/email.js"></script>
</body>

</html>