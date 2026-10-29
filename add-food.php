<?php
   session_start();
  
   $DATABASE_HOST = "localhost";
   $DATABASE_NAME = "food_ordering";
   $DATABASE_USER = "root";
   $DATABASE_PASS = "";
 
   $SID = $_SESSION["SID"];
   $Name = $_POST['Name'];
   $Price = $_POST['Price'];
   $Amount = $_POST['Amount'];
 
 
   try{
       if(!isset($Name) || !isset($Price)){
           header("Location: ./home.php");
           exit();
       }else if (empty($Name)|| empty($Price) || empty($Amount)) {
           throw new Exception('Please fill out all column.');
       }else if (strlen($Name) > 100) {                                             // Name
           throw new Exception('Length of product name out of range.');
       }else if ($Price < 1) {                                                     // Price
           throw new Exception('Price shoult be positive.');
       }else if ($Amount < 0) {                                                    // Amount
           throw new Exception('Amount shoult be positive.');
       }
       else if (!fopen($_FILES["myFile"]["tmp_name"], "rb")) {
           throw new Exception('Please upload photo.');
       }

       $file = fopen($_FILES["myFile"]["tmp_name"], "rb");
       $fileContents = fread($file, filesize($_FILES["myFile"]["tmp_name"]));
       fclose($file);
       $fileContents = base64_encode($fileContents);
      
       $conn = new PDO("mysql:host=$DATABASE_HOST; dbname=$DATABASE_NAME;", $DATABASE_USER, $DATABASE_PASS);
       $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       $stmt = $conn->prepare("select Name from product where Name =:name and SID = :sid");
       $stmt->execute(array('name' => $Name, 'sid' => $SID));
 
       if($stmt->rowCount() == 0){
          
           $stmt = $conn->prepare("insert into product(SID, Name, Price, Amount) values(:sid, :name, :price, :amount)");
           $stmt->execute(array('sid' => $SID, 'name' => $Name, 'price' => $Price, 'amount' => $Amount));
      
           $stmt = $conn->prepare("select PID from product where Name =:name");
           $stmt->execute(array('name' => $Name));
           $PID = $stmt->fetch()["PID"];
      
           $stmt = $conn->prepare("insert into image (PID, Image, ImageType) VALUES (:pid, :image, :type)");
           $stmt->execute(array('pid' => $PID, 'image' => $fileContents, 'type' => $_FILES["myFile"]["type"]));
      
           echo <<< EOT
               <!DOCTYPE html>
               <html>
                   <body>
                       <script>
                           alert("Add product successfully!");
                           window.location.replace("home.php");
                       </script>
                   </body>
               </html>
           EOT;
       }else{
           throw new Exception("This Product name already exists!");
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

