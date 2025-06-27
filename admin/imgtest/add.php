<?php
//add.php
require '../../config/db.php';


if (isset($_POST['submit'])) {
    $file_name = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $folder = "../../img/product/" . $file_name;

    // $target_dir = "../../img/product/"; // Ensure this directory exists and is writable
    // $target_file = $target_dir . basename($file_name);
    // echo $target_file;

    $query = mysqli_query($conn, "insert into image (image) values ('$file_name')");

    if (move_uploaded_file($tmp_name, $folder)) {
        echo "<script>console.log('file uploaded successfullly')</script>";
    } else {
        echo "<script>console.log('file not uploaded')</script>";
    }
}

// Check if the HTTP_REFERER header is set
if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    // Redirect the user back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit(); // Always call exit() after a header redirect
} else {
    // Fallback: If HTTP_REFERER is not set (e.g., direct access),
    // redirect to a default page, like your home or main image page.
    header("Location: imgtest.php"); // Replace 'imgtest.php' with your desired default
    exit(); // Always call exit() after a header redirect
}

?>