<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "novobank";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['loginid']) && isset($_POST['psw'])) {


    $pesel = $_POST['pesel'];
    $uname = $_POST['user_name'];
    $usurname = $_POST['user_surname'];
    $ubp = $_POST['user_birth_place'];
    $name = $_POST['loginid'];
    $psw = $_POST['psw'];

    
    $stmt = $conn->prepare("INSERT INTO users (pesel, user_name, user_surname, user_birth_place, loginid, psw) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $pesel, $uname, $usurname, $ubp, $name, $psw);

    if ($stmt->execute()) {
        echo "Twoje konto zostało utworzone. Możesz się teraz zalogować.";
    } else {
        echo "Wystąpił błąd podczas tworzenia konta: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Wprowadź login i hasło";
}


$conn->close();
?>