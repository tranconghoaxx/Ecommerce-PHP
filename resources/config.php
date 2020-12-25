<?php
// khoi dong qua trinh ghi cache vao bo dem khi can lay ra dung
ob_start();
session_start();
// session_destroy();
// dung duong dan cho ca window va linux
defined("DS") ? null : define("DS", DIRECTORY_SEPARATOR); 
defined("TEMPLATE_FRONT") ? null : define("TEMPLATE_FRONT", __DIR__ . DS . "templates/front");
defined("TEMPLATE_BACK")? null : define("TEMPLATE_BACK",__DIR__ . DS . "templates/back");
defined("UPLOAD_DIRECTORY")? null : define("UPLOAD_DIRECTORY",__DIR__ . DS . "uploads" );
// define path and database connection
defined("DB_HOST")? null : define("DB_HOST","localhost");
defined("DB_USER") ? null : define("DB_USER","root");
defined("DB_PASS") ? null : define("DB_PASS","");
defined("DB_NAME") ? null : define("DB_NAME","ecom_db");

$connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
require_once("functions.php");
require_once("cart.php");

?>