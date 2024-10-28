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

if (isset($_POST['loginid']) && isset($_POST['psw'])) {
    $name = $_POST['loginid'];
    $psw = $_POST['psw'];

    
    $stmt = $conn->prepare("SELECT loginid FROM users WHERE loginid = ? AND psw = ?");
    $stmt->bind_param("ss", $name, $psw);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['loginid'] = $name;
        header("Location: bank.php");  
        exit();
    } else {
        echo "Niepoprawny login lub hasło.";
    }

    $stmt->close();
} else {
    echo "Wprowadź login i hasło";
}

$conn->close();
?>
