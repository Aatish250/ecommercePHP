<?php
session_start();
require '../../config/db.php';


// name
// category
// price
// stock
// description
// image
if (isset($_POST['add_product']) && $_POST['add_product'] == 'add') {
    
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    
    $file_name = $_FILES['image']['name'];
    $file_tmp = $_FILES['image']['tmp_name'];
    
    $folder_path = "../../img/product/";
    $new_img_path = $folder_path . $file_name;
    
    if(!empty($file_name)){

        $sql_product = "INSERT INTO `products`
            (`product_name`, `image`, `product_price`, `category`, `stock`, `description`)
            VALUES ('$name', '$file_name','$price', '$category','$stock','$description')";
        $result = mysqli_query($conn, $sql_product);
        if ($result) {
            
            if (move_uploaded_file($file_tmp, $new_img_path)) {
                    $_SESSION['message-status'] = "success";
                $_SESSION['message'] = "Product Details Added";
            }
            
        }

    }

}
if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {

    echo (session_status() === PHP_SESSION_NONE) ? "No SESSION" : "YES SESSION";
    echo "<br> message: ".$_SESSION['message'];
    echo "<br> message-status: ".$_SESSION['message-status'];
    echo "<br>";
    echo "<a href='".$_SERVER['HTTP_REFERER']."'>".$_SERVER['HTTP_REFERER']."</a>";
    header('Location: ' . $_SERVER['HTTP_REFERER']);

    // $_SESSION['message-status'] = "success";
    // $_SESSION['message'] = "Created product successfully FROM HTTP REFFER";

    exit();
} else {
    header("Location: ../inventory.php");
    exit();
}

?>