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

    <div class="main-center">
    <h1 style="font-size: 300%; padding: 15px;">Witaj, <span style="color: green;"><?php echo htmlspecialchars($user_name);?> </span></h1>
    <h1>Stan konta: <span style="color: green;"><?php echo htmlspecialchars($balance);?></h1>

    <form action="./transfersystem.php" method="post">

    <label for="">Do kogo:</label>
    <input type="text" name="recipient">
    <label for="">Kwota</label>
    <input type="text" name="sum">
    <button type="submit">wyślij siano</button>
    </form>
    </div>




    
    <footer>
        <p>Strona została stworzona wyłącznie w celach edukacyjnych, mając na celu przybliżenie użytkownikom aspektów działania systemów bankowych, funkcjonalności oferowanych przez banki oraz metod zapewnienia bezpieczeństwa w bankowości elektronicznej. Materiały, układ graficzny oraz informacje zawarte na stronie są symulacją i nie mają na celu naśladowania żadnego istniejącego banku ani instytucji finansowej.

            Zastrzegamy, że zawartość strony nie jest rzeczywistą ofertą finansową ani też nie przedstawia rzeczywistych usług bankowych. Strona ma charakter wyłącznie dydaktyczny, służy pogłębieniu wiedzy z zakresu bankowości i bezpieczeństwa finansowego, a wszelkie dane przedstawione na niej są fikcyjne. Wszelkie nazwy, logotypy oraz elementy graficzne zostały stworzone wyłącznie na potrzeby edukacyjne.
            
            Podstawa prawna: Zgodnie z art. 16 ust. 2 ustawy o prawie autorskim i prawach pokrewnych, wykorzystywanie materiałów o charakterze dydaktycznym i edukacyjnym jest dozwolone w ramach tzw. dozwolonego użytku edukacyjnego. Wszystkie treści zamieszczone na tej stronie internetowej zostały stworzone w duchu edukacyjnym i nie mają na celu komercyjnego wykorzystywania w działalności gospodarczej.</p>
    </footer>


    
</body>
</html>
