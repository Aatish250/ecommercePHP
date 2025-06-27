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

    $sql_img = " INSERT INTO `image`(`image`) VALUES ('$file_name')";
    $result = mysqli_query($conn, $sql_img);

    if ($result) {
        $last_id = mysqli_insert_id($conn);

        $sql_product = "INSERT INTO `products`
            (`product_name`, `product_price`, `category`, `stock`, `description`, `img_id`)
            VALUES ('$name','$price', '$category','$stock','$description','$last_id')";
        $result = mysqli_query($conn, $sql_product);
        if ($result) {
            if (move_uploaded_file($file_tmp, $new_img_path)) {
                $_SESSION['message-status'] = "success";
                $_SESSION['message'] = "Product detail added successfully";
            }

        }
    }
}

if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    $_SESSION['message-status'] = "success";
    $_SESSION['message'] = "Created product successfully";

    exit();
} else {
    header("Location: ../inventory.php");
    exit();
}

?>