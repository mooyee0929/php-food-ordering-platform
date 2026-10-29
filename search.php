<?php
    session_start();
    $_SESSION['search'] = FALSE;
    $dbservername = 'localhost';
    $dbname = 'food_ordering';
    $dbusername = 'root';
    $dbpassword = '';
    $conn = new PDO("mysql:host=$dbservername; dbname=$dbname;", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $shopname = $_GET['shop'];
   
    $sel1 = $_GET['sel1'];
    $lowerbound = $_GET['lowerbound'];
    $upperbound = $_GET['upperbound'];
    $meals = $_GET['meal'];
    $categories = $_GET['category'];
    $longitude = $_SESSION['Longitude'];
    $latitude = $_SESSION['Latitude'];
    $stmt = $conn->prepare("SELECT Name FROM store");
    $stmt->execute();
    $result = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'Name');
 
    if(!empty($_GET['shop'])){
        $stmt = $conn->prepare("SELECT Name FROM store WHERE Name LIKE '%" . $shopname . "%'");
        $stmt->execute();
        $shopname_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'Name');
        $result = array_intersect($result, $shopname_set);
        $result = array_values($result);
    }
    if(!empty($_GET['category'])){
        $stmt = $conn->prepare("SELECT Name, Category FROM store WHERE Category = :categories");
        $stmt->execute(array("categories" => $categories));
        $category_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'Name');
        $result = array_intersect($result, $category_set);
        $result = array_values($result);
    }
    if(!empty($_GET['sel1'])){
        if($sel1 == 'near')
            $stmt = $conn->prepare("SELECT Name, Category FROM store WHERE
                                    'SQRT(SQUARE($latitude-Latitud)+SQUARE($longitude-Longitude))' < 500 " );
        else if ($sel1 == 'medium')
            $stmt = $conn->prepare("SELECT Name, Category FROM store
                                    WHERE 'SQRT(SQUARE($latitude-Latitud)+SQUARE($longitude-Longitude))' between 500 and 1000" );
        else
            $stmt = $conn->prepare("SELECT Name, Category FROM store
                                    WHERE 'SQRT(SQUARE($latitude-Latitud)+SQUARE($longitude-Longitude))' > 1000" );
 
        $stmt->execute();
        $dis_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'Name');
 
        $result = array_intersect($result, $dis_set);
        $result = array_values($result);
    }
    if(!empty($_GET['meal'])){
        $stmt = $conn->prepare(" SELECT store.Name FROM store INNER JOIN product ON ( store.SID = product.SID and product.Name = '$meals' )");
        $stmt->execute();
        $meals_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'Name');
        $result = array_intersect($result, $meals_set);
        $result = array_values($result);
    }
 
    if(!empty($_GET['lowerbound']) && !empty($_GET['upperbound'])){
        $stmt = $conn->prepare(" SELECT DISTINCT store.Name FROM store INNER JOIN product ON  store.SID = product.SID  WHERE Price BETWEEN $lowerbound and $upperbound ");
        echo "test1";
        $stmt->execute();
        $money_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'Name');
        $result = array_intersect($result, $money_set);
        $result = array_values($result);
    }
    else if (!empty($_GET['lowerbound'])){
        $stmt = $conn->prepare(" SELECT DISTINCT store.Name FROM store INNER JOIN product ON  store.SID = product.SID  WHERE Price >= $lowerbound ");
        $stmt->execute();
        $money_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'Name');
        $result = array_intersect($result, $money_set);
        $result = array_values($result);
    }
    else if (!empty($_GET['upperbound'])){
        $stmt = $conn->prepare(" SELECT DISTINCT store.Name FROM store INNER JOIN product ON  store.SID = product.SID  WHERE Price <= $upperbound ");
        $stmt->execute();
        $money_set = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'Name');
        $result = array_intersect($result, $money_set);
        $result = array_values($result);
    }
 
    $count = 0;
    $final_names = get_str($result);
    $final_category = array();
    $final_distance = array();
 
    $command = "SELECT name, category
                FROM store WHERE 'SQRT(SQUARE($latitude-Latitud)+SQUARE($longitude-Longitude))' < 500
                and name IN " . $final_names ;
    $stmt = $conn->prepare($command);
    $stmt->execute();
    $tmp_category = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'category');
    for($i = 0; $i < count($tmp_category); $i++,$count++){
        $final_category[$count] = $tmp_category[$i];
        $final_distance[$count] = 'near';
    }
    $command = "SELECT name, category
                FROM store WHERE 'SQRT(SQUARE($latitude-Latitud)+SQUARE($longitude-Longitude))' between 500 and 1000
                and name IN " . $final_names ;
    $stmt = $conn->prepare($command);
    $stmt->execute();
    $tmp_category = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'category');
    for($i = 0; $i < count($tmp_category); $i++){
        $final_category[$count] = $tmp_category[$i];
        $final_distance[$count] = 'medium';
    }
 
    $command = "SELECT name, category
                FROM store WHERE 'SQRT(SQUARE($latitude-Latitud)+SQUARE($longitude-Longitude))' > 1000
                and name IN " . $final_names ;
 
    $stmt = $conn->prepare($command);
    $stmt->execute();
    $tmp_category = get_attr_arr($stmt->fetchAll(PDO::FETCH_CLASS), 'category');
    for($i = 0; $i < count($tmp_category); $i++){
        $final_category[$count] = $tmp_category[$i];
        $final_distance[$count] = 'far';
    }
 
 
 
    // $show = "";
 
    // for($i = 0; $i < count($result); $i++){
    //     $show = $show . '<tr>' .
    //                         '<th scope="row">' . ($i+1) . '</th>' .
    //                         '<td>' . $result[$i] . '</td>' .
    //                         '<td>' . $final_category[$i] . '</td>' .
    //                         '<td>' . $final_distance[$i] . '</td>' .
    //                         '<td> <button type="button" class="btn btn-info " data-toggle="modal" data-target="#'.$result[$i].'"> Open menu</button></td>' .
    //                     '</tr>';
       
       
    // }
    $_SESSION['shoplist'] = $result;
    $_SESSION['categorylist'] = $final_category;
    $_SESSION['distancelist'] = $final_distance;
    echo <<< EOT
    <!DOCTYPE html>
    <html>
        <body>
            <script>
                window.location.replace("home.php");
            </script>
        </body>
    </html>
    EOT;
 
    function get_attr_arr($arr, $attr){
        $name = array();
        for($i = 0; $i < count($arr); $i++)
            $name[] = $arr[$i]->$attr;
        return $name;
    }
    function get_str($arr){
 
        if(count($arr) > 0){
            $name = "(";
            for($i = 0; $i < count($arr) - 1; $i++)
                $name = $name . "'" . $arr[$i] . "'" . ",";
           
            $name = $name . "'" . $arr[count($arr) - 1] . "'" . ")";
        }
        else{
            $name = "('')";
        }
        return $name;
    }
?>
 

