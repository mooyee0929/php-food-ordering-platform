<?php
   session_start();
   $_SESSION['Authenticated'] = false;
 
   // log in database
   $DATABASE_HOST = "localhost";
   $DATABASE_NAME = "food_ordering";
   $DATABASE_USER = "root";
   $DATABASE_PASS = "";
 
   $Name = $_POST['Name'];
   $PhoneNumber = $_POST['PhoneNumber'];
   $Account = $_POST['Account'];
   $Password = $_POST['Password'];
   $Repassword = $_POST['Re-password'];
   $Longitude = $_POST['Longitude'];
   $Latitude = $_POST['Latitude'];
 
   try{
       if(!isset($Account) || !isset($Password)){
           header("Location: loginpage.php");
           exit();
       }else if (empty($Name)|| empty($PhoneNumber) || empty($Account) || empty($Password) || empty($Repassword) || empty($Latitude) || empty($Longitude)){
           throw new Exception('Please fill out all column.');
       }else if (!ctype_alpha($Name)) {                                                   // Name
           throw new Exception('Name should be English without whitespace.');
       }else if (!is_numeric($PhoneNumber)) {                                             // Phone Number
           throw new Exception('PhoneNumber should be a number.');
       }else if (strlen($PhoneNumber) != 10) {
           throw new Exception('Length of PhoneNumber should be 10.');
       }else if (!ctype_alnum($Account)) {                                                // Account
           throw new Exception('Account should only consist of alphabet & number.');
       }else if (strlen($Account) > 255) {                            
           throw new Exception('Length of Account out of range.');
       }else if (!ctype_alnum($Password)) {                                               // Password
           throw new Exception('Password should only consist of alphabet & number.');
       }else if (strlen($Password) > 255) {                                      
           throw new Exception('Length of Password out of range.');
       }else if ($Password != $Repassword) {
           throw new Exception('Password confirmation failed.');
       }else if (!is_numeric($Latitude)) {                                                // Latitude
           throw new Exception('Latitude should be a number.');
       }else if ($Latitude > 90 || $Latitude < -90) {
           throw new Exception('Latitude out of range.');
       }else if (!is_numeric($Longitude)) {                                               // Longitude
           throw new Exception('Longitude should be a number.');
       }else if ($Longitude > 180 || $Longitude < -180) {
           throw new Exception('Longitude out of range.');
       }
       $conn = new PDO("mysql:host=$DATABASE_HOST; dbname=$DATABASE_NAME;", $DATABASE_USER, $DATABASE_PASS);
       $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       $stmt = $conn->prepare("select Name from users where Name =:Account");
       $stmt->execute(array('Account' => $Account));
 
       if($stmt->rowCount() == 0){
           $Salt = strval(rand(1000, 9999));
           $Hashpassword = hash('sha256', $Salt.$Password);
           $stmt = $conn->prepare("insert into users(Name, Password, Salt, Account, PhoneNumber, Longitude, Latitude) values(:Name, :Password, :Salt, :Account, :PhoneNumber, :Longitude, :Latitude)");
           $stmt->execute(array('Name' => $Name, 'Password' => $Hashpassword, 'Salt' => $Salt,
                                'Account' => $Account, 'PhoneNumber' => $PhoneNumber, 'Longitude' => $Longitude, 'Latitude' => $Latitude));
           //$_SESSION['Authenticated'] = true;
           $_SESSION['Account'] = $Account;
           echo <<< EOT
               <!DOCTYPE html>
               <html>
                   <body>
                       <script>
                           alert("Registration success.");
                           window.location.replace("loginpage.php");
                       </script>
                   </body>
               </html>
           EOT;
           exit();
       }else{
           throw new Exception("This Account has been registered.");
       }
   }
   catch(Exception $e){
       $msg = $e->getMessage();
       session_unset();
       session_destroy();
       echo <<< EOT
           <!DOCTYPE html>
           <html>
               <body>
                   <script>
                       alert("$msg");
                       window.location.replace("registerpage.php");
                   </script>
               </body>
           </html>
       EOT;
       exit();
   }
?>