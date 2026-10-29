<?php
 session_start();
 // log in database
 $DATABASE_HOST = "localhost";
 $DATABASE_NAME = "food_ordering";
 $DATABASE_USER = "root";
 $DATABASE_PASS = "";
 
 // collect data from post
 $UID = $_SESSION["UID"];
 $Account = $_SESSION["Account"];
 $Name = $_POST['ShopName'];
 $Category = $_POST['ShopCategory'];
 $Latitude = $_POST['Latitude'];
 $Longitude = $_POST['Longitude'];
 
 // check limitation
 try{
   if(!isset($Name) || !isset($Category)){
     header("Location: home.php");
     exit();
   }else if (empty($Name)|| empty($Category) || empty($Latitude) || empty($Longitude)) {
     throw new Exception('Please fill out all column.');
   }else if (strlen($Name) > 255) {                                                  // Name
     throw new Exception('Length of ShopName out of range.');
   }else if (!ctype_alnum($Name)) {
     throw new Exception('ShopName should only consisted of alphabet & number.');
   }else if (strlen($Category) > 255) {                                              // Category
     throw new Exception('Length of ShopCategory out of range.');
   }else if (!is_numeric($Latitude)) {                                               // Latitude
     throw new Exception('Latitude should be a number.');
   }else if ($Latitude > 90 || $Latitude < -90) {
     throw new Exception('Latitude out of range.');
   }else if (!is_numeric($Longitude)) {                                              // Longitude
     throw new Exception('Longitude should be a number.');
   }else if ($Longitude > 180 || $Longitude < -180) {
     throw new Exception('Longitude out of range.');
   }
   $conn = new PDO("mysql:host=$DATABASE_HOST; dbname=$DATABASE_NAME;", $DATABASE_USER, $DATABASE_PASS);
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   $stmt = $conn->prepare("select Name from store where Name =:Name");
   $stmt->execute(array('Name' => $Name));
   if($stmt->rowCount() == 0){
     $stmt = $conn->prepare("insert into store(Name, Category, Latitude, Longitude) values(:shopname, :category, :latitude, :longitude)");
     $stmt->execute(array('shopname' => $Name, 'category' => $Category, 'latitude' => $Latitude, 'longitude' => $Longitude));
      $stmt = $conn->prepare("select SID from store where Name =:shopname");
     $stmt -> execute(array('shopname' => $Name));
     $SID = $stmt->fetch()["SID"];
      $stmt = $conn->prepare("insert into manager(UID, SID) values(:uid, :sid)");
     $stmt->execute(array('uid' => $UID, 'sid' => $SID));
     echo <<< EOT
       <!DOCTYPE html>
       <html>
         <body>
           <script>
             alert("Store register successfully!");
             window.location.replace("./home.php");
           </script>
         </body>
       </html>
     EOT;
   }else{
     throw new Exception("This ShopName already exists!");
   }
 }
 // return error message
 catch(Exception $e){
   $msg = $e -> getMessage();
   echo <<< EOT
     <!DOCTYPE html>
     <html>
       <body>
         <script>
           alert("$msg");
           window.location.replace("home.php");
         </script>
       </body>
     </html>
   EOT;
 }
?>