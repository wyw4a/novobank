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

    $name = $_POST['loginid'];
    $psw = $_POST['psw'];

    
    $sql = "SELECT * FROM users WHERE login_id='$name' AND password='$psw' ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        header("Location: konto.html");
        exit();
    } else {
        echo "Niepoprawny login lub hasło.";
    }
} else {
    echo "Wprowadź login i hasło";
}

$conn->close();
?>