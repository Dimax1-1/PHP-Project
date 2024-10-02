<?php
// Includi il file di configurazione del database
require 'config.php';

// Prova a connetterti al database
try {
    $conn = new PDO("pgsql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Errore di connessione: " . $e->getMessage();
    exit();
}
?>

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

    <h2>Elenco Clienti</h2>

    <?php
    // Recupera i clienti dal database
    $query = $conn->query("SELECT Nome, NumeroPolizza, Scadenza, Premio, Parcella, Agenzia, Pagato, Frazionamento, DataPagamento FROM clienti");
    $clienti = $query->fetchAll(PDO::FETCH_ASSOC);

    if ($clienti) {
        echo "<table class='content-table'>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Numero Polizza</th>
                    <th>Scadenza</th>
                    <th>Premio</th>
                    <th>Parcella</th>
                    <th>Agenzia</th>
                    <th>Pagato</th>
                    <th>Frazionamento</th>
                    <th>Data Pagamento</th>
                </tr>
            </thead>
            <tbody>";
        foreach ($clienti as $cliente) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($cliente['Nome']) . "</td>";
            echo "<td>" . htmlspecialchars($cliente['NumeroPolizza']) . "</td>";
            echo "<td>" . htmlspecialchars($cliente['Scadenza']) . "</td>";
            echo "<td>" . htmlspecialchars($cliente['Premio']) . "€</td>";
            echo "<td>" . htmlspecialchars($cliente['Parcella']) . "€</td>";
            echo "<td>" . htmlspecialchars($cliente['Agenzia']) . "</td>";
            echo "<td>" . ($cliente['Pagato'] ? 'Sì' : 'No') . "</td>";
            echo "<td>" . ($cliente['Frazionamento'] == 'A' ? 'Annuale' : 'Semestrale') . "</td>";
            echo "<td>" . htmlspecialchars($cliente['DataPagamento']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Nessun cliente trovato.</p>";
    }
    ?>

</body>

</html>
