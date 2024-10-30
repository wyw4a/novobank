<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "novobank";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper function to prepare, bind, execute, and close a statement
function executeQuery($conn, $query, $params) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param(...$params);
    $stmt->execute();
    $stmt->close();
}

if (isset($_SESSION['loginid']) && isset($_POST['do_kogo']) && isset($_POST['kwota'])) {
    $nadawca_id = $_SESSION['loginid'];
    $odbiorca_id = $_POST['do_kogo'];
    $kwota = $_POST['kwota'];

    // Retrieve the sender's balance
    $stmt = $conn->prepare("SELECT saldo FROM uzytkownicy WHERE id = ?");
    $stmt->bind_param("i", $nadawca_id);
    $stmt->execute();
    $stmt->bind_result($saldo_nadawcy);
    $stmt->fetch();
    $stmt->close();

    if ($saldo_nadawcy >= $kwota && $kwota > 0) {
        $conn->begin_transaction();
        try {
            // Deduct amount from sender
            executeQuery($conn, "UPDATE uzytkownicy SET saldo = saldo - ? WHERE id = ?", ["di", $kwota, $nadawca_id]);
            
            // Add amount to receiver
            executeQuery($conn, "UPDATE uzytkownicy SET saldo = saldo + ? WHERE id = ?", ["di", $kwota, $odbiorca_id]);

            // Insert transaction into history for sender
            executeQuery($conn, "INSERT INTO historia (uzytkownik_id, kto, do_kogo, kwota, suma) VALUES (?, 'Ja', ?, ?, ?)", ["isdd", $nadawca_id, $odbiorca_id, $kwota, -$kwota]);
            
            // Insert transaction into history for receiver
            executeQuery($conn, "INSERT INTO historia (uzytkownik_id, kto, do_kogo, kwota, suma) VALUES (?, ?, 'Ja', ?, ?)", ["isdd", $odbiorca_id, $nadawca_id, $kwota, $kwota]);

            $conn->commit();
            echo "Przelew wykonano pomyślnie!";
        } catch (Exception $e) {
            $conn->rollback();
            echo "Błąd: " . $e->getMessage();
        }
    } else {
        echo "Błąd: Niewystarczające środki na koncie lub niepoprawna kwota.";
    }
} else {
    echo "Błąd: Brak danych lub użytkownik niezalogowany.";
}

$conn->close();
?>
