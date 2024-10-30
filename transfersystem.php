<?php
session_start();
$servername = "localhost";
$username = 'root';
$password = '';
$database = 'novobank';

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Połączenie nieudane: " . $conn->connect_error);
}

function executeQuery($conn, $query, $params) {
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Błąd przygotowania zapytania: " . $conn->error);
    }
    $stmt->bind_param(...$params);
    if (!$stmt->execute()) {
        die("Błąd wykonania zapytania: " . $stmt->error);
    }
    $stmt->close();
}

if (isset($_SESSION['loginid']) && isset($_POST["recipient"]) && isset($_POST['sum'])) {
    $sender_id = $_SESSION['loginid'];
    $recipient_id = $_POST['recipient'];
    $sum = $_POST['sum'];

    
    echo "Nadawca: $sender_id, Odbiorca: $recipient_id, Kwota: $sum<br>";

    $stmt = $conn->prepare("SELECT balance FROM users WHERE loginid = ?");
    $stmt->bind_param('s', $sender_id); 
    $stmt->execute();
    $stmt->bind_result($sum_balance);
    $stmt->fetch();
    $stmt->close();

    echo "Saldo nadawcy: $sum_balance<br>"; 

    if ($sum_balance >= $sum && $sum > 0) {
        $conn->begin_transaction();
        try {
            
            executeQuery($conn, "UPDATE users SET balance = balance - ? WHERE loginid = ?", ['ds', $sum, $sender_id]);
            executeQuery($conn, "UPDATE users SET balance = balance + ? WHERE loginid = ?", ['ds', $sum, $recipient_id]);

            executeQuery($conn, "INSERT INTO history (loginid, who_send, who_receive, moni, sum_bal) VALUES (?, ?, 'Ja', ?, ?)", ['isdd', $sender_id, $recipient_id, $sum, -$sum]);
            executeQuery($conn, "INSERT INTO history (loginid, who_send, who_receive, moni, sum_bal) VALUES (?, 'Ja', ?, ?, ?)", ['isdd', $sender_id, $recipient_id, $sum, $sum]);

            $conn->commit();
            echo "Przelew wykonany!";
        } catch (Exception $e) {
            $conn->rollback();
            echo "Błąd podczas transakcji: " . $e->getMessage();
        }
    } else {
        echo "Brak wystarczających środków.";
    }
} else {
    echo "Błąd! Nieprawidłowe dane wejściowe.";
}

$conn->close();
?>