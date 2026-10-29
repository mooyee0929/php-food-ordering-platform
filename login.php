<?php
   session_start();
   $_SESSION['Authenticated'] = false;
 
   $DATABASE_HOST = "localhost";
   $DATABASE_NAME = "food_ordering";
   $DATABASE_USER = "root";
   $DATABASE_PASS = "";
 
   $Account = $_POST['Account'];
   $Password = $_POST['Password'];
    try{
       if(!isset($Account) || !isset($Password)){
           header("Location: loginpage.php");
           exit();
       }
      
       if(empty($Account) || empty($Password)){
           throw new Exception("Please fill out all column.");
       }
 
       $conn = new PDO("mysql:host=$DATABASE_HOST; dbname=$DATABASE_NAME;", $DATABASE_USER , $DATABASE_PASS);
       $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       $stmt = $conn->prepare("select Account, Password, Name, Longitude, Latitude, PhoneNumber, Balance, Salt from users where Account =:Account");
       $stmt -> execute(array('Account' => $Account));
  
       if($stmt -> rowCount() > 0){
           $row = $stmt->fetch();
           if ($row['Password'] == hash('sha256', $row['Salt'].$Password)){
               $_SESSION['Authenticated'] = true;
               $_SESSION['Account'] = $row['Account'];
               $_SESSION['Name'] = $row['Name'];
               $_SESSION['Longitude'] = $row['Longitude'];
               $_SESSION['Latitude'] = $row['Latitude'];
               $_SESSION['PhoneNumber'] = $row['PhoneNumber'];
               $_SESSION['Balance'] = $row['Balance'];
               $_SESSION['Authenticated'] = true;
               header("Location: home.php");
               exit();
           }else{
               throw new Exception("Incorrect account or password.");
           }
       }else{
           throw new Exception("Account doesn't exist.");
       }
   }
   catch(Exception $e){
       $msg = $e -> getMessage();
       session_unset();
       session_destroy();
       echo <<< EOT
           <!DOCTYPE html>
           <html>
               <body>
                   <script>
                       alert("$msg");
                       window.location.replace("loginpage.php");
                   </script>
               </body>
           </html>
       EOT;
   }
?>
 

