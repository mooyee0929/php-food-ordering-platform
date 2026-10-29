<?php
header('Content-Type: application/json; charset=UTF-8');
$dbservername = 'localhost';
$dbname = 'food_ordering';
$dbusername = 'root';
$dbpassword = '';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $Name = $_POST["Name"];
    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT Name from store where Name =:Name");
    $stmt->execute(array('Name' => $Name));

    if($stmt->rowCount() != 0){
        echo json_encode(array(
            'Name' => $Name
        ));
    }
    else if($Name == null){
        echo json_encode(array(
            'Msg' => 'Shop Name should be filled.'
    ));
    }
    else {
        echo json_encode(array(
            'Msg' => 'Shop Name valid!'
        ));
    }
} else {
    echo json_encode(array(
        'Msg' => 'Error!'
    ));
}
?>