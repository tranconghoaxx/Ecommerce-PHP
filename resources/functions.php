<?php
$upload_directory = "uploads";
// Helper functions
function last_id(){
    global $connection;
    return mysqli_insert_id($connection);
}
function set_message($msg){
    if(!empty($msg)){
        $_SESSION['message'] = $msg;
    }else{
        $msg = "";
    }
}
function display_message(){
    if(isset($_SESSION['message'])){
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}
function redirect($location){
    header("Location: $location ");
}

function query($sql){
    global $connection;
    return mysqli_query($connection,$sql);
}

function confirm($result){
    global $connection;
    if(!$result){
        die("QUERY FAILED  " . mysqli_error($connection));
    }
}

function escape_string($string){
    global $connection;
    // chong sql injection
    return mysqli_real_escape_string($connection,$string);
}

function fetch_array($result){
    return mysqli_fetch_array($result);
}
//*************************FRONT END FUNCTIONS */
// get products
function get_products(){
   $query =  query("SELECT * FROM products");
   confirm($query);
   while($row  = fetch_array($query)){
    $product_image = display_image($row['product_image']);
      $product = <<<DELIMETER
                <div class="col-sm-4 col-lg-4 col-md-4">
                        <div class="thumbnail">
                         <a href="item.php?id={$row['product_id']}">   <img src="../resources/{$product_image}" alt=""></a>
                            <div class="caption">
                            <h4><a href="item.php?id={$row['product_id']}">{$row["product_title"]}</a>
                            </h4>
                                <h4 class="">&#36;{$row["product_price"]}</h4>
                               
                                <p>{$row['short_desc']}</p>
                                <a class="btn btn-primary" target="_blank"
                                    href="../resources/cart.php?add={$row['product_id']}">Add to cart</a>
                            </div>
                        </div>
                </div>
      DELIMETER;
      echo $product;
   }
}
function get_categories(){
    $query = query("SELECT * FROM categories");
    confirm($query);
    while($row = fetch_array($query)){
   
        $cate = <<<DELIMETER
            <a href='category.php?id={$row['cat_id']}' class='list-group-item'>{$row['cat_title']}</a>
        DELIMETER;
        echo $cate;
    }
}
function get_products_in_cat_page(){
    $query =  query("SELECT * FROM products WHERE product_category_id="  .escape_string($_GET['id']) . "");
    confirm($query);
    while($row = fetch_array($query)){
        $product_image = display_image($row['product_image']);
        $product = <<<DELIMETER
        <div class="col-md-3 col-sm-6 hero-feature">
            <div class="thumbnail">
                <img src="../resources/$product_image" alt="">
                <div class="caption">
                    <h3>{$row['product_title']}</h3>
                    <p>{$row['short_desc']}</p>
                    <p>
                        <a href="../resources/cart.php?add={$row['product_id']}" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$row['product_id']}" class="btn btn-default">More Info</a>
                    </p>
                </div>
            </div>
        </div>
        DELIMETER;
        echo $product;
    }
}
function get_products_in_shop_page(){
    $query = query("SELECT * FROM products");
    confirm($query);
    while($row = fetch_array($query)){
        $product_image = display_image($row['product_image']);
        $product = <<<DELIMETER
        <div class="col-md-3 col-sm-6 hero-feature">
            <div class="thumbnail">
                <img src="../resources/$product_image" alt="">
                <div class="caption">
                    <h3>{$row['product_title']}</h3>
                    <p>{$row['short_desc']}</p>
                    <p>
                        <a href="../resources/cart.php?add={$row['product_id']}" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$row['product_id']}" class="btn btn-default">More Info</a>
                    </p>
                </div>
            </div>
        </div>
        DELIMETER;
        echo $product;
    }
}
function login_user(){
    if(isset($_POST['submit'])){
       $username = escape_string($_POST['username']);
       $password = escape_string($_POST['password']);
       $query = query("SELECT * FROM users WHERE username = '{$username}' AND password='${password}' ");
        confirm($query);
        if(mysqli_num_rows($query) == 0){
            set_message("Your password or username are wrong!");
            redirect("login.php");
        }else{
            $_SESSION['username'] = $username;
            set_message("Welcome to admin ${username}");
            redirect("admin");
        }
    }
}
function send_message(){
    if(isset($_POST['submit'])){
        $to = "tranhoa300696@gmail.com";
        $name = $_POST['name'];
        $subject = $_POST['subject'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        $headers = "Form: {$name} ${email}";
        $result = mail($to,$subject,$message,$headers);
        if(!$result){
            set_message("Sorry we could not send your message");
            redirect('contact.php');
        }else{
            set_message("Your message has been sent");
            redirect('contact.php');
        }
    }
}
//*************************BACK END FUNCTIONS */
function display_orders(){
    global $connection;
    $query = "SELECT * FROM orders";
    $send_query = mysqli_query($connection,$query);
    if(!$send_query){
        die("QUERY FAILED " + mysqli_error($connection));
    }
    while($row = mysqli_fetch_array($send_query)){
        $orders = <<<DELIMETER
                <tr>
                    <th>{$row['order_id']}</th>
                    <th>{$row['order_amount']}</th>
                    <th>{$row['order_transaction']}</th>
                    <th>{$row['order_currency']}</th>
                    <th>{$row['order_status']}</th>
                    <td><a class="btn btn-danger" href="../../resources/templates/back/delete_order.php?id={$row['order_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
         
                    
                </tr>
        DELIMETER;
        echo $orders;
    }
}
/*********************************Admin Product  Page*/
function display_image($picture){
    global $upload_directory;
    return $upload_directory . DS . $picture;
}
function get_product_in_admin(){
    $query = "SELECT * FROM products";
    $send_query = mysqli_query(mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME),$query);
    if(!$send_query){
        die("Query Failed " . mysqli_error(mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME)));
    }
    while($row = mysqli_fetch_array($send_query)){
        $category = show_product_category_title($row['product_category_id']);
       $product_image = display_image($row['product_image']);
        $product = <<<DELIMETER
            <tr>
                <td>{$row['product_id']}</td>
                <td>{$row['product_title']} <br>
                <a class="" href="index.php?edit_product&id={$row['product_id']}">

                <img width='100' src="../../resources/$product_image" alt="photo">
                
                </a>
                </td>
                <td>{$category}</td>
                <td>{$row['product_price']}</td>
                <td>{$row['product_quantity']}</td>
                <td><a class="btn btn-danger" href="../../resources/templates/back/delete_product.php?id={$row['product_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
            </tr>
        DELIMETER;
        echo $product;
    }
}
/*********************************Add Product In Admin */
function add_product(){
    defined("DB_HOST") ? null : define('DB_HOST','localhost');
    defined('DB_USER')? null : define("DB_USER",'root');
    defined("DB_PASS") ? null : define("DB_PASS",'');
    defined("DB_NAME")? null: define("DB_NAME","ecom_db");
    $connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    if(isset($_POST['publish'])){
        $product_title = mysqli_real_escape_string($connection,$_POST['product_title']);
        $product_category_id = mysqli_real_escape_string($connection,$_POST['product_category_id']);
        $product_price = mysqli_real_escape_string($connection,$_POST['product_price']);
        $product_description = mysqli_real_escape_string($connection,$_POST['product_description']);
        $short_desc = mysqli_real_escape_string($connection,$_POST['short_desc']);
        $product_quantity = mysqli_real_escape_string($connection,$_POST['product_quantity']);
        $product_image = mysqli_real_escape_string($connection,$_FILES['file']['name']);
        // dung cai nay khong the they file in folder
        $image_temporary_location = mysqli_real_escape_string($connection,$_FILES['file']['tmp_name']);
        defined("UPLOAD_DIRECTORY") ? null : define("UPLOAD_DIRECTORY", __DIR__ . DS . "uploads");
        move_uploaded_file($_FILES["file"]["tmp_name"],UPLOAD_DIRECTORY . DS . $product_image);
        $query = "INSERT INTO products (product_title,product_category_id,product_description,short_desc,product_price,product_quantity,product_image) VALUES('${product_title}','${product_category_id}','${product_description}','${short_desc}','${product_price}','${product_quantity}','${product_image}')";
        $send_query = mysqli_query($connection,$query);
        $last_id = mysqli_insert_id($connection);
        if(!$send_query){
            die("Failed Query add product " . mysqli_error($connection));
        }
        $_SESSION['message'] = "New Product with id {$last_id} was Added";
        redirect("index.php?products");
    

    }
}
function show_cate_add_product(){
    global $connection;
    $query = "SELECT * FROM categories";
    $send_query = mysqli_query($connection,$query);
    if(!$send_query){
        die("failed show cate add product " . mysqli_error($connection));
    }
    while($row = mysqli_fetch_array($send_query)){
        $categories_options = <<<DELIMETER
            <option value="{$row['cat_id']}">{$row['cat_title']}</option>
        DELIMETER;
        echo $categories_options;
    }
}
function show_product_category_title($product_category_id){
    global $connection;
    $category_query = "SELECT * FROM categories WHERE cat_id = '{$product_category_id}' ";
    $send_query = mysqli_query($connection,$category_query);
    if(!$send_query){
        die("Failed query " . mysqli_error($connection) );
    }
    while($row = mysqli_fetch_array($send_query)){
        return $row['cat_title'];
    }
}
/**************************Update Product */
function update_product(){
    defined("DB_HOST") ? null : define('DB_HOST','localhost');
    defined('DB_USER')? null : define("DB_USER",'root');
    defined("DB_PASS") ? null : define("DB_PASS",'');
    defined("DB_NAME")? null: define("DB_NAME","ecom_db");
    $connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    if(isset($_POST['update'])){
        $product_title = mysqli_real_escape_string($connection,$_POST['product_title']);
        $product_category_id = mysqli_real_escape_string($connection,$_POST['product_category_id']);
        $product_price = mysqli_real_escape_string($connection,$_POST['product_price']);
        $product_description = mysqli_real_escape_string($connection,$_POST['product_description']);
        $short_desc = mysqli_real_escape_string($connection,$_POST['short_desc']);
        $product_quantity = mysqli_real_escape_string($connection,$_POST['product_quantity']);
        $product_image = mysqli_real_escape_string($connection,$_FILES['file']['name']);
        // dung cai nay khong the they file in folder
        $image_temporary_location = mysqli_real_escape_string($connection,$_FILES['file']['tmp_name']);

        if(empty($product_image)){
            $get_pic = "SELECT product_image FROM products WHERE product_id = ".mysqli_real_escape_string($connection,$_GET['id']) ." ";
            $send_query = mysqli_query($connection,$get_pic);
            if(!$send_query){
                die("failed query get picture ". mysqli_error($connection));
            }
            while($pic = mysqli_fetch_array($send_query)){
                $product_image = $pic['product_image'];
            }
        }

        defined("UPLOAD_DIRECTORY") ? null : define("UPLOAD_DIRECTORY", __DIR__ . DS . "uploads");
        move_uploaded_file($_FILES["file"]["tmp_name"],UPLOAD_DIRECTORY . DS . $product_image);
        
        $query = "UPDATE products SET ";
        $query .= "product_title ='$product_title' ,";
        $query .= "product_category_id ='$product_category_id' ,";
        $query .= "product_price ='$product_price' ,";
        $query .= "product_description ='$product_description' ,";
        $query .= "short_desc ='$short_desc' ,";
        $query .= "product_quantity ='$product_quantity' ,";
        $query .= "product_image ='$product_image' ";

        $query .= "WHERE product_id=" . mysqli_real_escape_string($connection,$_GET['id']);


        $send_query = mysqli_query($connection,$query);
        if(!$send_query){
            die("Failed Query add product " . mysqli_error($connection));
        }

        $_SESSION['message'] = "Product has been updated";

        redirect("index.php?products");
    }
}
/****************Category in admin */
function show_categories_in_admin(){
    $query = "SELECT * FROM categories";
    global $connection;
    $category_query = mysqli_query($connection,$query);
    if(!$category_query){
        die("Failed query in showcate in admin " . mysqli_error($connection));

    }
    while($row = mysqli_fetch_array($category_query)){
        $cat_id = $row['cat_id'];
        $cat_title = $row['cat_title'];
        $category = <<<DELIMETER
                <tr>
                <td>{$cat_id}</td>
                <td>{$cat_title}</td>
                <td><a class="btn btn-danger" href="../../resources/templates/back/delete_category.php?id={$row['cat_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
                </tr>
        DELIMETER;
        echo $category;
    }
}
function add_category(){
    global $connection;
    if(isset($_POST['add_category'])){
        $cat_title = mysqli_real_escape_string($connection,$_POST['cat_title']);
        if(empty($cat_title) || $cat_title == ' '){
            echo "<p class='bg-danger' >THIS CAN NOT BE EMPTY </p>";
        }else{
            $query = "INSERT INTO categories(cat_title) VALUES('{$cat_title}') ";
            $send_query = mysqli_query($connection,$query);
            if(!$send_query){
                die("Failed query in add category ". mysqli_error($connection));
            }
            $_SESSION['message'] = "CATEGORY CREATED";
            // redirect("index.php?categories");
        }
    }
}
?>