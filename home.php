<?php
session_start();
if($_SESSION['Authenticated'] == false){
header("Location: loginpage.php");
exit();
}
 // log in database
$DATABASE_HOST = "localhost";
$DATABASE_USER = "root";
$DATABASE_PASS = "";
$DATABASE_NAME = "food_ordering";
$storename = "effww";
$conn = new PDO("mysql:host=$DATABASE_HOST; dbname=$DATABASE_NAME;", $DATABASE_USER, $DATABASE_PASS);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$Account = $_SESSION["Account"];
$stmt = $conn->prepare("select UID from users where Account =:Account");
$stmt -> execute(array('Account' => $Account));
$UID = $stmt->fetch()["UID"];
$_SESSION["UID"] = $UID;
$stmt = $conn->prepare("select SID from manager where UID =:uid");
$stmt -> execute(array('uid' => $UID));
$SID = $stmt->fetch()["SID"];
$_SESSION["SID"] = $SID;
if(isset($_GET['lat']) && isset($_GET['lon'])){
 $Longitude = $_GET['lon'];
 $Latitude = $_GET['lat'];
 $stmt = $conn->prepare("update users SET Longitude = :Longitude, Latitude = :Latitude where Account = :Account");
 $stmt->execute(array('Account' => $Account, 'Longitude' => $Longitude, 'Latitude' => $Latitude ));
 $_SESSION["Longitude"] = $Longitude;
 $_SESSION["Latitude"] = $Latitude;
 echo <<< EOT
 <!DOCTYPE>
     <html>
         <body>
             <script>
                 window.location.replace("./home.php")
             </script>
         </body>
 </html>
 EOT;
}
if(isset($_GET['bal'])){
$UID = $_SESSION["UID"];
$Balance = $_GET['bal'];
$Action = "Recharge";
$time = date('m-d-Y h:i:s a', time());
$add = $_GET['amount'];
//建立交易紀錄
$stmt = $conn->prepare("insert into record(UID1, UID2, Amount, Time, Action) values(:uid1, :uid2, :amount, :time, :action)");
$stmt->execute(array('uid1' => $UID, 'uid2' => $UID, 'amount' => $add, 'time' => $time, 'action' => $Action));
 
//修改餘額
$stmt = $conn->prepare("update users SET Balance = :Balance where Account = :Account");
$stmt->execute(array('Account' => $Account, 'Balance' => $Balance));
$_SESSION["Balance"] = $Balance;
echo <<< EOT
<!DOCTYPE>
    <html>
        <body>
            <script>
                alert("Recharge success.");
                window.location.replace("./home.php")
            </script>
        </body>
</html>
EOT;
}
if(isset($_GET['pid']) && isset($_GET['price']) && isset($_GET['amount'])){
 $pid= $_GET['pid'];
 $price = $_GET['price'];
 $amount = $_GET['amount'];
 $stmt = $conn->prepare("update product SET Price = :price, Amount = :amount where PID = :pid");
 $stmt->execute(array('price' => $price, 'amount' => $amount, 'pid' => $pid));
 echo <<< EOT
 <!DOCTYPE>
     <html>
         <body>
             <script>
                 window.location.replace("./home.php")
             </script>
         </body>
 </html>
 EOT;
}else if(isset($_GET['pid'])){
 $pid= $_GET['pid'];
 $stmt = $conn->prepare("DELETE FROM product WHERE PID=:pid");
 $stmt->execute(array('pid' => $pid));
 $stmt = $conn->prepare("DELETE FROM image WHERE PID=:pid");
 $stmt->execute(array('pid' => $pid));
 echo <<< EOT
 <!DOCTYPE>
     <html>
         <body>
             <script>
                 window.location.replace("./home.php")
             </script>
         </body>
 </html>
 EOT;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<title>小恐龍套餐</title>
</head>
<body>
<nav class="navbar navbar-inverse">
<div class="container-fluid">
  <div class="navbar-header">
    <a class="navbar-brand " href="#">&#x1F996早餐吃貓包包&#x1F996</a>
  </div>
</div>
</nav>
<div class="container">
<ul class="nav nav-tabs">
  <li class="active"><a href="#home">Home</a></li>
  <li><a href="#menu1">Shop</a></li>
  <li><a href="#menu2">MyOrder</a></li>
  <li><a href="#menu3">Shop Order</a></li>
  <li><a href="#menu4">Transaction Record</a></li>
  <li><a href="login.php">Logout</a></li>
</ul>
<div class="tab-content">
  <div id="home" class="tab-pane fade in active">
    <h3>Profile &#x1F995</h3>
    <div class="row">
      <div class="col-xs-12">
      <?php
      $Account = $_SESSION["Account"];
      $Name = $_SESSION["Name"];
      $PhoneNumber = $_SESSION["PhoneNumber"];
      $Longitude = $_SESSION["Longitude"];
      $Latitude = $_SESSION["Latitude"];
      echo "Accouont: $Account, Name: $Name, PhoneNumber: $PhoneNumber,  location: $Longitude, $Latitude"
      ?>
   
        <button type="button " style="margin-left: 5px;" class=" btn btn-info " data-toggle="modal"
        data-target="#location">edit location</button>
        <div class="modal fade" id="location"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog  modal-sm">
            <div class="modal-content">
         
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">edit location</h4>
              </div>
              <div class="modal-body">
               <label class="control-label " for="longitude">longitude</label>
                <input type="text" class="form-control" id="longitude" name="Longitude" placeholder="enter longitude">
                  <br>
                  <label class="control-label " for="latitude">latitude</label>
                <input type="text" class="form-control" id="latitude" name="Latitude" placeholder="enter latitude">
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-default" id=editlocation  name="save" data-dismiss="modal">Edit</button>
           
              </div>
            </div>
          </div>
        </div>
        <script>
          $('#editlocation').click( function(){
             let latitude = $('#latitude').val();
             let longitude = $('#longitude').val();
             if(longitude === "" || latitude === "")
                 alert("input error!");
             else if(isNaN(longitude))
                 alert("longitude should be a number!");
             else if(isNaN(latitude))
                 alert("latitude should be a number!");
             else if(latitude < -90 || latitude > 90)
                 alert("latitude error!");
             else if (longitude < -180 || longitude > 180)
                 alert("longitude error!");
             else
                 window.location.replace("./home.php?lat=" + latitude + "&lon=" + longitude);
          });
        </script>
   
        <?php
        $Balance = $_SESSION["Balance"];
        echo "walletbalance: $Balance";
        ?>
        <!-- Modal -->
        <button type="button " style="margin-left: 5px;" class=" btn btn-info " data-toggle="modal"
          data-target="#myModal">Add value</button>
        <div class="modal fade" id="myModal"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog  modal-sm">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add value</h4>
              </div>
              <div class="modal-body">
                <input type="text" class="form-control" id="value" placeholder="enter add value">
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-default" id=addvalue  name="save" data-dismiss="modal">Add</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
          $('#addvalue').click( function(){
             <?php
               echo "var jsBalance ='$Balance';";
             ?>
             let add_balance = $('#value').val();
             if(add_balance === "")
                 alert("balance should not be blank!");
             else if(isNaN(add_balance))
                 alert("balance should be a number!");
             else if(parseInt(add_balance) != parseFloat(add_balance))
                 alert("balance should be a integer!");
             else if(add_balance <= 0)
                 alert("balance should be positive!");
             else{
                 jsBalance = parseInt(jsBalance) + parseInt(add_balance);
                 window.location.replace("./home.php?bal=" + jsBalance + "&amount=" + add_balance);
             }
          });
        </script>
    <!--
       
         -->
    <h3>Search &#x1F432</h3>
    <div class=" row  col-xs-8">
      <form class="form-horizontal" action="search.php" method="get">
        <div class="form-group">
          <label class="control-label col-sm-1" for="Shop">Shop</label>
          <div class="col-sm-5">
            <input type="text" id="shop" name="shop" class="form-control" placeholder="Enter Shop name">
          </div>
          <label class="control-label col-sm-1" for="distance">distance</label>
          <div class="col-sm-5">
            <select class="form-control" id="sel1" name="sel1">
              <option>near</option>
              <option>medium </option>
              <option>far</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-1" for="Price">Price</label>
          <div class="col-sm-2">
            <input type="text" id="lowerbound" name="lowerbound" class="form-control">
          </div>
          <label class="control-label col-sm-1" for="~">~</label>
          <div class="col-sm-2">
            <input type="text" id="upperbound" name="upperbound" class="form-control">
          </div>
          <label class="control-label col-sm-1" for="Meal">Meal</label>
          <div class="col-sm-5">
            <input type="text" list="Meals" class="form-control" id="Meal" name="meal" placeholder="Enter Meal">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-1" for="category"> category</label>
   
     
            <div class="col-sm-5">
              <input type="text" list="categorys" class="form-control" id="category" name="category" placeholder="Enter shop category">
            </div>
            <button type="submit" id="searchsubmit" style="margin-left: 18px;"class="btn btn-primary">Search</button>
     
        </div>
      </form>
    </div>
     <div class="row">
         <div class="  col-xs-8">
         <?php
              $dbservername = 'localhost';
              $dbusername ='root';
              $dbpassword = '';
              $dbname = 'food_ordering';  
              $shopresult = empty($_SESSION['shoplist'])?array():$_SESSION['shoplist'];
              $categoryresult = empty($_SESSION['categorylist'])?array():$_SESSION['categorylist'];
              $distanceresult = empty($_SESSION['distancelist'])?array():$_SESSION['distancelist'];
              $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
              $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
              echo "<table class='table' style=' margin-top: 15px;'>
                  <thead>
                  <tr>
                    <th scope='col'>#</th>
                    <th scope='col'>shop name</th>
                    <th scope='col'>shop category</th>
                    <th scope='col'>Distance</th>
                  </tr>
                  </thead>";
                echo "<tbody>";
                for($i = 0; $i < count($shopresult); $i++) {
                  echo '<tr>' .
                        '<th scope="row">' . ($i+1) . '</th>' .
                        '<td>' . $shopresult[$i] . '</td>' .
                        '<td>' . $categoryresult[$i] . '</td>' .
                        '<td>' . $distanceresult[$i] . '</td>' .
                        '<td> <button type="button" class="btn btn-info " data-toggle="modal" data-target="#'.$shopresult[$i].'"> Open menu</button></td>' .
                        '</tr>';
                }
                  echo "</tbody>
             </table>"?>
             <?php
                for($i = 0; $i < count($shopresult); $i++){
                //$SID = $_SESSION["SID"];
              
                $stmt = $conn->prepare("select SID from store where Name = '$shopresult[$i]'");
                $stmt->execute();
                $sssid = $stmt->fetch(PDO::FETCH_OBJ);
                $ssid = $sssid->SID;
                $conn = mysqli_connect($dbservername, $dbusername, $dbpassword, $dbname);
                $sql = "select PID from product where SID = $ssid";
                $result = mysqli_query($conn, $sql);
           
                $picture = array();
                $picturetype = array();
                if($result){
                  for($r = 0; $r < mysqli_num_rows($result); $r++){
                    $PID = mysqli_fetch_row($result)[0];
                    $sql = "select Image, ImageType from image where PID = $PID";
                    $data = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_row($data);
                    if($data){
                      $pic = $row[0];
                      $type = $row[1];
                      $picture[] = $pic;
                      $picturetype[] = $type;
                    }
                  }
                }
                $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $conn->prepare("SELECT product.Name, Price, Amount FROM  store INNER JOIN product ON store.SID = product.SID   where store.Name =  '" .$shopresult[$i]. "' ");
                $stmt->execute();
                $row = $stmt->fetchAll(PDO::FETCH_CLASS);
                $product = get_attr_arr($row, "Name");
                $price = get_attr_arr($row, "Price");
                $amount = get_attr_arr($row, "Amount");
                echo '<div class="modal fade" id="'.$shopresult[$i].'"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">'.
                      '<div class="modal-dialog">'.
             
                      '<div class="modal-content">'.
                      '<div class="modal-header">'.
                      '<button type="button" class="close" data-dismiss="modal">&times;</button>'.
                      '<h4 class="modal-title">menu</h4>'.
                    '</div>'.
                    '<div class="modal-body">'.
           
                    '<div class="row">'.
                      '<div class="  col-xs-12">'.
                        '<table class="table" style=" margin-top: 15px;">'.
                          '<thead>'.
                            '<tr>'.
                              '<th scope="col"></th>'.
                              '<th scope="col">Picture</th>'.
                         
                              '<th scope="col">meal name</th>'.
                       
                              '<th scope="col">price</th>'.
                              '<th scope="col">Quantity</th>'.
                         
                              '<th scope="col">Order check</th>'.
                            '</tr>'.
                          '</thead>'.
                          '<tbody>';
                            for($j=0;$j<count($product);$j++){
                              echo '<tr>' .
                              '<th scope="row">' . ($j+1) . '</th>' .
                              '<td>' . '<img alt="Embedded Image" src="data:' . $picturetype[$j] .';base64 ,'. $picture[$j] .'" style="width:80px; height: 80px;"/>' . '</td>' .
                              '<td>' . $product[$j] . '</td>' .
                              '<td>' . $price[$j] . '</td>' .
                              '<td>' . $amount[$j]. '</td>' .
                              '<td><input type="checkbox" id="cbox2" value="'.$product[$j].'"></td>'.
                              '</tr>';
                            }
 
                          echo '</tbody>'.
                        '</table>'.
                      '</div>'.
 
                    '</div>'.
                    '</div>'.
                    '<div class="modal-footer">'.
                      '<button type="button" class="btn btn-default" data-dismiss="modal">Order</button>'.
                    '</div>'.
                  '</div>'.
               
                '</div>'.
              '</div>';
              }
             ?>
         </div>
     </div>
   </div>
   <div id="menu2" class="tab-pane fade">
    <label class="control-label col-sm-1" for="status">Status</label>
    <div class="col-sm-5">
      <select class="form-control" id="sel1" name="sel1">
       <option>All</option>
       <option>Finished</option>
       <option>Not Finish</option>
       <option>Cancel</option>
      </select>
    </div>
   </div>
   <div id="menu3" class="tab-pane fade">
   <label class="control-label col-sm-1" for="status">Status</label>
    <div class="col-sm-5">
      <select class="form-control" id="sel1" name="sel1">
       <option>All</option>
       <option>Finished</option>
       <option>Not Finish</option>
       <option>Cancel</option>
      </select>
    </div>
   </div>
   <div id="menu4" class="tab-pane fade">
   <label class="control-label col-sm-1" for="action">Action</label>
    <div class="col-sm-5">
      <select class="form-control" id="sel1" name="sel1">
       <option>All</option>
       <option>Payment</option>
       <option>Receive</option>
       <option>Recharge</option>
      </select>
    </div>
   </div>
   <div id="menu1" class="tab-pane fade">
   <form id="demo" action="shop.php" method="post" class="fh5co-form animate-box" data-animate-effect="fadeIn">
    <h3> Start a business &#x1F958</h3>
    <div class="form-group ">
        <div class="row">
            <div class="col-xs-2">
                <label for="ex5">shop name</label>
                <input class="form-control" id="ex5" name="ShopName" placeholder="macdonald" type="text" >
                <p id="result"></p>
                <button type="button" id="verify">Verification (by AJAX)</button>
            </div>
            <div class="col-xs-2">
            <label for="ex5">shop category</label>
            <input class="form-control" id="ex6" name="ShopCategory" placeholder="fast food" type="text" >
            </div>
            <div class="col-xs-2">
            <label for="ex8">longitude</label>
            <input class="form-control" id="ex8" name="Longitude" placeholder="121.00028167648875" type="text" >
            </div>
            <div class="col-xs-2">
            <label for="ex6">latitude</label>
            <input class="form-control" id="ex7" name="Latitude" placeholder="24.78472733371133" type="text" >
            </div>
        </div>
    </div>
    <div class=" row" style=" margin-top: 25px;">
        <div class=" col-xs-3">
          <input type="submit"  id="shopregister" value="Register" class="btn btn-primary">
        </div>
    </div>
  </form>
  <script>
         $("#verify").click(function() {
           $.ajax({
             type: "POST",
             url: "ajaxshop.php",
             dataType: "json",
             data: {
               Name: $("#ex5").val(),
             },
             success: function(data) {
               if (data.Name) {
                 $("#demo")[0].reset();
                 $("#result").html('Shop Name ' + data.Name + ' has already been registered!');
               } else {
                 $("#result").html(data.Msg);
               }
             },
             error: function(jqXHR) {
               $("#demo")[0].reset();
               $("#result").html('Error: ' + jqXHR.status);
             }
           })
         })    
  </script>
    <script>
        function disable(){
          var shopname =
          '<?php
            $conn = new PDO("mysql:host=$DATABASE_HOST; dbname=$DATABASE_NAME;", $DATABASE_USER, $DATABASE_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("select SID from manager where UID =:uid");
            $stmt -> execute(array('uid' => $UID));
            $shopsid = $stmt->fetch()["SID"];
            $stmt = $conn->prepare("select Name from store where SID =:sid");
            $stmt -> execute(array('sid' => $shopsid));
            $storename = $stmt->fetch()["Name"];
            $stmt = $conn->prepare("select Name, Category, Latitude, Longitude  from store where Name =:Name");
            $stmt -> execute(array('Name' => $storename));
            $row = $stmt->fetch()["Name"];
            echo $row;
          ?>';
          var category =
          '<?php
            $conn = new PDO("mysql:host=$DATABASE_HOST; dbname=$DATABASE_NAME;", $DATABASE_USER, $DATABASE_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
            $stmt = $conn->prepare("select Name, Category, Latitude, Longitude  from store where Name =:Name");
            $stmt -> execute(array('Name' => $storename));
            $row = $stmt->fetch()["Category"];
            echo $row;
          ?>';
          var latitude =
          '<?php
            $conn = new PDO("mysql:host=$DATABASE_HOST; dbname=$DATABASE_NAME;", $DATABASE_USER, $DATABASE_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
            $stmt = $conn->prepare("select Name, Category, Latitude, Longitude  from store where Name =:Name");
            $stmt -> execute(array('Name' => $storename));
            $row = $stmt->fetch()["Latitude"];
            echo $row;
          ?>';
          var longitude =
          '<?php
            $conn = new PDO("mysql:host=$DATABASE_HOST; dbname=$DATABASE_NAME;", $DATABASE_USER, $DATABASE_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
            $stmt = $conn->prepare("select Name, Category, Latitude, Longitude  from store where Name =:Name");
            $stmt -> execute(array('Name' => $storename));
            $row = $stmt->fetch()["Longitude"];
            echo $row;
          ?>';
          document.getElementById("ex5").value = shopname;
          document.getElementById("ex6").value = category;
          document.getElementById("ex7").value = latitude;
          document.getElementById("ex8").value = longitude;
          document.getElementById("shopregister").disabled = true;
          document.getElementById("verify").disabled = true;
          document.getElementById("ex5").disabled = true;
          document.getElementById("ex6").disabled = true;
          document.getElementById("ex7").disabled = true;
          document.getElementById("ex8").disabled = true;
        }
    </script>
    <?php
      $conn = new PDO("mysql:host=$DATABASE_HOST; dbname=$DATABASE_NAME;", $DATABASE_USER, $DATABASE_PASS);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $conn->prepare("select SID from manager where UID =:uid");
      $stmt -> execute(array('uid' => $UID));
      if($stmt -> rowCount() > 0){
        echo '<script> disable(); </script>';
      }
    ?>
    <h3>ADD &#x1F963</h3>
    <form action = "add-food.php" method="post" Enctype="multipart/form-data">
    <div class="form-group ">
      <div class="row">
        <div class="col-xs-6">
          <label for="ex3">meal name</label>
          <input class="form-control" id="ex3" name="Name" type="text">
        </div>
      </div>
      <div class="row" style=" margin-top: 15px;">
        <div class="col-xs-3">
          <label for="ex7">price</label>
          <input class="form-control" id="ex7" name="Price" type="text">
        </div>
        <div class="col-xs-3">
          <label for="ex4">quantity</label>
          <input class="form-control" id="ex4" name="Amount" type="text">
        </div>
      </div>
      <div class="row" style=" margin-top: 25px;">
        <div class=" col-xs-3">
          <label for="ex12">上傳圖片</label>
          <input id="myFile" type="file" name="myFile" multiple class="file-loading">
        </div>
        <div class=" col-xs-3">
          <button style=" margin-top: 15px;" type="submit" class="btn btn-primary">Add</button>
        </div>
      </div>
    </div>
    </form>
    <div class="row">
      <div class="  col-xs-8">
        <table class="table" style=" margin-top: 15px;">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Picture</th>
              <th scope="col">meal name</th>
     
              <th scope="col">price</th>
              <th scope="col">Quantity</th>
              <th scope="col">Edit</th>
              <th scope="col">Delete</th>
            </tr>
          </thead>
          <tbody>
          <?php
$dbservername = 'localhost';
$dbname = 'food_ordering';
$dbusername = 'root';
$dbpassword = '';
$conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//$SID = $_SESSION["SID"];
$stmt = $conn->prepare("select PID from product where SID =:sid");
$stmt->execute(array('sid' => $SID));
$pid = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'PID');
$stmt = $conn->prepare("select Name from product where SID =:sid");
$stmt->execute(array('sid' => $SID));
$result0 = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'Name');
$stmt = $conn->prepare("select Price from product where SID =:sid");
$stmt->execute(array('sid' => $SID));
$result1 = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'Price');
$stmt = $conn->prepare("select Amount from product where SID =:sid");
$stmt->execute(array('sid' => $SID));
$result2 = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'Amount');
$conn = mysqli_connect($dbservername, $dbusername, $dbpassword, $dbname);
$sql = "select PID from product where SID = $SID";
$result = mysqli_query($conn, $sql);
$picture = array();
$picturetype = array();
if($result){
for($i = 0; $i < mysqli_num_rows($result); $i++){
  $PID = mysqli_fetch_row($result)[0];
  $sql = "select Image, ImageType from image where PID = $PID";
  $data = mysqli_query($conn, $sql);
  $row = mysqli_fetch_row($data);
  if($data){
    $pic = $row[0];
    $type = $row[1];
    $picture[] = $pic;
    $picturetype[] = $type;
  }
}
}
$show = "";
for($i = 0; $i < count($result0); $i++){
   $show = $show . '<tr>' .
                       '<th scope="row">' . ($i+1) . '</th>' .
                       '<td>' . '<img alt="Embedded Image" src="data:' . $picturetype[$i] .';base64 ,'. $picture[$i] .'" style="width:80px; height: 80px;"/>' . '</td>' .
                       '<td>' . $result0[$i] . '</td>' .
                       '<td>' . $result1[$i] . '</td>' .
                       '<td>' . $result2[$i] . '</td>' .
                       '<td><button type="button" class="btn btn-info" data-toggle="modal" data-target="#coffee-1">Edit</button></td>'.
                       '<td><button type="submit" id="' . $pid[$i] . '"class="btn btn-danger">Delete</button></td>'.
                       '<div class="modal fade" id="coffee-1" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                           <div class="modal-dialog" role="document">
                               <div class="modal-content">
                               <div class="modal-header">
                                   <h5 class="modal-title" id="staticBackdropLabel">Edit product</h5>
                                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                                   </button>
                               </div>
                               <div class="modal-body">
                                   <div class="row" >
                                   <div class="col-xs-6">
                                       <label for="ex72">price</label>
                                       <input class="form-control" id="ex72" type="text">
                                   </div>
                                   <div class="col-xs-6">
                                       <label for="ex42">quantity</label>
                                       <input class="form-control" id="ex42" type="text">
                                   </div>
                                   </div>
                   
                               </div>
                               <div class="modal-footer">
                                   <button type="submit" id="' . $result0[$i] . '"class="btn btn-secondary" data-dismiss="modal">Edit</button>
                               </div>
                               </div>
                           </div>
                           </div>' .
                   '</tr>' .
                   '<script>
                   $("#'. $result0[$i] .'").click( function(){' .
                       'let pid = ' . $pid[$i]  . ';' .
                       'let price = $("#ex72").val();
                       let amount = $("#ex42").val();
                       if(price === "" || amount === "")
                           alert("input error!");
                       else if(isNaN(price))
                           alert("Price should be a number.");
                       else if (isNaN(amount))
                           alert("Amount should be a number.");
                       else if(price < 1)
                           alert("price error!");
                       else if (amount < 0)
                           alert("amount error!");
                       else
                           window.location.replace("./home.php?pid=" + ' . 'pid' . ' + "&price=" + price + "&amount=" + amount);
                       });
                   </script>' .
                   '<script>' .
                     '$("#' . $pid[$i] . '").click( function(){' .
                         'let pid = ' . $pid[$i]  . ';' .
                         'window.location.replace("./home.php?pid=" + ' . 'pid' . ');' .
                     '});'.
                   '</script>';
}
echo $show;
function get_attr_arr($arr, $attr){
   $name = array();
   for($i = 0; $i < count($arr); $i++)
       $name[] = $arr[$i]->$attr;
   return $name;
}
?>
         </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
<!-- Option 1: Bootstrap Bundle with Popper -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> -->
<script>
$(document).ready(function () {
  $(".nav-tabs a").click(function () {
    $(this).tab('show');
  });
});
</script>
<!-- Option 2: Separate Popper and Bootstrap JS -->
<!--
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
-->
</body>
</html>
 
 
 

