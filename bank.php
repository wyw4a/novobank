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


if(!isset($_SESSION['loginid'])){
    echo "Zaloguj się, aby zobaczyć informacje o koncie.";
    exit();
}

$loginid = $_SESSION['loginid'];


$stmt = $conn->prepare("SELECT balance, user_name, user_surname FROM users WHERE loginid = ?");
$stmt->bind_param("s", $loginid);
$stmt->execute();
$result = $stmt->get_result();

$balance = 0;
$user_name = '';
$user_surname = '';

if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $balance = $row['balance'];
    $user_name = $row['user_name'];
    $user_surname = $row['user_surname'];
} else {
    echo "Brak danych.";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleBankAccount.css">
    <link rel="icon" type="image/x-icon" href="logo.png">
    <title>NovoBank</title>
</head>
<body>
    <div class="menu-top">
        <div class="menu-left">
            <a href="./index.html"><img src="logo.png" alt="" width="200px" height="100px"></a>
        </div>
        <div class="menu-right">
            <button><a href="./register-page.html">OTWÓRZ KONTO</a></button>
            <button><a href="./kredyt.html">WEŹ KREDYT</a></button>
            <button><a href="./login_page.html">ZALOGUJ SIĘ</a></button>
        </div>
    </div>

    <h1>Witaj, <span style="color: green;"><?php echo htmlspecialchars($user_name); ?></span></h1>
    <h1>Imię: <?php echo htmlspecialchars($user_name); ?></h1>
    <h1>Nazwisko: <?php echo htmlspecialchars($user_surname); ?></h1>
    <h1>Stan Konta: <?php echo htmlspecialchars($balance); ?></h1>
    
</body>
</html>
