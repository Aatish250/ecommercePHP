<?php

require '../../config/db.php';

// this is for showing all category options
if (isset($_POST['showOption']) && $_POST["showOption"]) {

    echo "<option value=''>All Category</option>";
    $previousOption = $_POST['previousCategory'];
    $optionResult = mysqli_query($conn, "SELECT DISTINCT category FROM products;");
    while ($option = mysqli_fetch_assoc($optionResult)) {
        echo "<option ";
        if ($option['category'] == $_POST['previousCategory'])
            echo "selected";
        echo ">";
        echo $option['category'] . "</option>";
    }
    exit();
}

// this is for showing all data
if (isset($_POST['showData']) && $_POST["showData"]) {

    session_start();
    $search = $_POST['search'];
    $selectedCategory = $_POST['category'];

    if (!empty($_POST['category']))
        $queryCategory = "AND category = '" . $_POST['category'] . "'";
    else
        $queryCategory = '';

    // echo "Given Seach: " . $search . "<br>Given Category: " . $queryCategory . "<br><br>";


    $query = "SELECT * FROM products WHERE product_name LIKE '%$search%' $queryCategory";
    // echo $query;
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {

            $product_id = $row['product_id'];
            $product_name = $row['product_name'];
            $product_price = $row['product_price'];
            $stock = $row['stock'];
            $category = $row['category'];
            $description = $row['description'];
            $img_id = $row['img_id'];

            $img_result = mysqli_query($conn, "SELECT * FROM image where img_id = $img_id");
            $img_db = mysqli_fetch_assoc($img_result);
            $img_path = "../img/product/" . $img_db['image'];

            echo "
                <div id='item' class='bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow'>
                <img src='$img_path' class='w-full h-48 object-cover'>
        
                <div class='p-4'>
                    <h3 class='text-lg font-semibold text-gray-800 mb-2'>
                    $product_name
                    </h3>
                    <p class='text-gray-600 text-sm mb-3 line-clamp-3'>
                        $description
                    </p>
                    <div class='flex justify-between items-center'>
                    <span class='text-xl font-bold text-indigo-600'>
                    Rs. $product_price
                    </span>
                    <a href='product_detail.php?id=$product_id&search=$search&previous-category=$selectedCategory'
                    class='bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition-colors'>
                    View Details
                    </a>
                    </div>
                </div>
                </div>
            ";
        }
    }
}
?>