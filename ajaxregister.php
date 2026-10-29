<?php
header('Content-Type: application/json; charset=UTF-8');
$dbservername = 'localhost';
$dbname = 'food_ordering';
$dbusername = 'root';
$dbpassword = '';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $Account = $_POST["Account"];
    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT Account from users where Account =:Account");
    $stmt->execute(array('Account' => $Account));

    if($stmt->rowCount() != 0){
        echo json_encode(array(
            'Account' => $Account
        ));
    }
    else if($Account == null){
        echo json_encode(array(
            'Msg' => 'Account should be filled.'
    ));
    }
    else {
        echo json_encode(array(
            'Msg' => 'Account valid!'
        ));
    }
} else {
    echo json_encode(array(
        'Msg' => 'Error!'
    ));
}
?>