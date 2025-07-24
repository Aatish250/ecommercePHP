<?php
//home_products.php
require '../../config/db.php';

// this is for showing all category options
if (isset($_POST['showOption']) && $_POST["showOption"]) {

    echo "<option value=''>All Category</option>";
    $previousOption = $_POST['previousCategory'];

    $optionResult = mysqli_query($conn, "SELECT DISTINCT category FROM products;");
    while ($option = mysqli_fetch_assoc($optionResult)) {
        echo "<option ";
        if ($option['category'] == $previousOption)
            echo "value='$previousOption' selected";
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
            $img_path = "../img/product/" . $row['image'];

            echo "
                <div id='item' class='bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow'>
                <a href='product_detail.php?product_id=$product_id&search=$search&previous-category=$selectedCategory' class='group'>
                    <img src='$img_path' class='w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105'>
                </a>
        
                <div class='p-4'>
                    <h3 class='text-lg font-semibold text-gray-800 mb-2'>
                        $product_name
                    </h3>
                    <div class='flex justify-between items-center mb-2'>
                        <span class='text-sm text-gray-500'>
                            Category: <span class='font-medium text-gray-700'>$category</span>
                        </span>
                        <span class='text-sm text-gray-500'>
                            Stock: <span class='font-medium text-gray-700'>$stock</span>
                        </span>
                    </div>
                    <div class='flex justify-between items-center'>
                        <span class='text-xl font-bold text-indigo-600'>
                            Rs. $product_price
                        </span>
                    </div>
                </div>
                </div>
            ";
        }
    }
    // session_unset();
}
?>