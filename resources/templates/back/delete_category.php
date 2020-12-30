<?php
require_once("../../config.php");


if(isset($_GET['id'])){
    $connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    $query = "DELETE FROM categories WHERE cat_id = " . mysqli_real_escape_string($connection,$_GET['id'])  . "";
    $send_query = mysqli_query($connection,$query);
    if(!$send_query){
        die("Query Failed " . mysqli_error($connection));
    }
    $_SESSION['message'] = "Category Deleted";
    

    redirect("../../../public/admin/index.php?categories");
}else{
    redirect("../../../public/admin/index.php?categories");
}
?>