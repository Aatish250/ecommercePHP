<?php
//imgtestinner.php
require '../../config/db.php';

if (isset($_POST['turncate'])) {

    $query = mysqli_query($conn, "TRUNCATE `products`");
    if ($query) {
        $files = glob("../../img/product/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                echo "Deleted: " . $file;
                unlink($file);
            }
        }
    }
}

if (isset($_POST['delete'])) {

    $id = $_POST['delete'];

    $result = mysqli_query($conn, "SELECT * FROM products where product_id = $id");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $image = $folder = "../../img/product/" . $row['image'];
        echo ("<script>console.log('Deleting: " . $image . "')</script>");
        if (file_exists($image))
            unlink($image);
    }

    mysqli_query($conn, "DELETE FROM products WHERE `product_id` = $id");


}

$result = mysqli_query($conn, "select * from products");
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "
            <input type='checkbox' value='" . $row['product_id'] . "' name='item-checkbox'>
            <button type='button' value='" . $row['product_id'] . "' class='w-15 p-2 m-1 rounded-md bg-red-300 text-red-600' onclick='deleteThis(this)'>Delete</button>
            <img src='../../img/product/" . htmlspecialchars($row['image']) . "' alt='uploaded image'>
            ";
    }
} else {
    echo "No Data found";
}
?>
</div>