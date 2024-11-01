<?php
// Connessione al database PostgreSQL
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verifica se l'email è già registrata
    $query = "SELECT * FROM utenti WHERE email = '$email'";
    $result = pg_query($conn, $query);

    if (pg_num_rows($result) > 0) {
        $message = "Email già registrata!";
        header("Location: ../index.html?message=" . urlencode($message));
    } else {
        // Inserisci nuovo utente nel database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO utenti (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
        
        $result_insert = pg_query($conn, $insert_query);
        $row = pg_fetch_assoc($result_insert);
        $id_user = $row['id'];

        $message = "Registrazione avvenuta con successo!";
        // Reindirizza di nuovo alla pagina HTML passando il messaggio via GET
        header("Location: ../VPS/index.html?message=" . urlencode($message) . "&id=" . urlencode($id_user));
        exit();
    }

}
?>