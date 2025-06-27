<!DOCTYPE html>
<html lang="en">

<?php

require '../config/db.php';
session_start();

if (isset($_POST['add_to_cart'])) {
    $quantity = $_POST['quantity'];
    $product_id = $_POST['product_id'];
    $user_id = $_POST['user_id'];

    $existItem = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id");
    if (mysqli_num_rows($existItem)) {
        echo "Dublicate";
        $insertSql = "UPDATE cart SET quantity = $quantity WHERE user_id = $user_id AND product_id = $product_id";
        $_SESSION['message'] = "Updating Cart";
    } else {
        echo "Inserting";
        $_SESSION['message'] = "Adding to Cart";
        $insertSql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id,$product_id,$quantity)";
    }

    $sendResult = mysqli_query($conn, $insertSql);
    if ($sendResult)
        $_SESSION['message-status'] = 'success';
    else
        $_SESSION['message-status'] = 'fail';

    header('Location: homepage.php');
    exit();
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require '../components/link_imports.php'; ?>
    <title>Document</title>
</head>

<body>

    <?php
    $active_page = 0;
    include '../components/user_nav.php';

    $product_id = $_GET['product_id'];
    // need to manage user ---------------------------------------
    $user_id = 1;


    $_SESSION['previous-search'] = $_GET['search'];
    $_SESSION['previous-category'] = $_GET['previous-category'];

    $result = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $product_id");
    $item = mysqli_fetch_assoc($result);
    $img_id = $item['img_id'];

    $img_result = mysqli_query($conn, "SELECT * FROM image where img_id = $img_id");
    $img_db = mysqli_fetch_assoc($img_result);
    $img_path = "../img/product/" . $img_db['image'];
    ?>


    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-4">
            <a href="homepage.php" class="text-indigo-600 hover:text-indigo-800">&larr; Back to Homepage</a>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="md:flex">
                <div class="md:w-1/2">
                    <img src="<?php echo $img_path ?>" alt="<?php echo $img_db['image'] ?>"
                        class="w-full h-96 object-cover">
                </div>

                <div class="md:w-1/2 p-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4">
                        <?php echo $item['product_name'] ?>
                    </h1>

                    <div class="mb-4">
                        <span
                            class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700">
                            <?php echo $item['category'] ?>
                        </span>
                    </div>

                    <p class="text-gray-600 mb-6 leading-relaxed">
                        <?php echo $item['description'] ?>
                    </p>

                    <div class="mb-6">
                        <span class="text-3xl font-bold text-indigo-600">
                            Rs. <?php echo $item['product_price'] ?>
                        </span>
                    </div>

                    <div class="mb-6">
                        <p class="text-sm text-gray-600">
                            Stock: <?php echo $item['stock'] ?> available</span>
                        </p>
                    </div>

                    <!-- <form method="POST" class="space-y-4">
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <input name="quantity" id="quantity" value="1" type="number" min="1" data-db-stock = <?php echo $item['stock']; ?>
                                max="<?php echo $item['stock']; ?>"
                                class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <button type="submit" name="add_to_cart"
                            class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                            Add to Cart
                        </button>
                    </form> -->

                    <form method='POST' class='space-y-4'>
                        <?php if ($item['stock'] > 0) {
                            echo "<div>
                                <label for='quantity' class='block text-sm font-medium text-gray-700 mb-2'>Quantity</label>
                                <input name='quantity' id='quantity' value='1' type='number' min='1' data-db-stock = " . $item['stock'] . " max='" . $item['stock'] . "'
                                    class='w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500'>
                                <input type='hidden' name='product_id' value='$product_id'>
                                <input type='hidden' name='user_id' value='$user_id'>
                            </div>
    
                            <button type='submit' name='add_to_cart'
                                class='w-full bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition-colors font-semibold'>
                                Add to Cart
                            </button>";
                        } else {
                            echo '
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            This product is currently out of stock.
                            </div>';
                        } ?>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        $("#quantity").change(function () {
            item = ($(this));
            dbStock = item.data("db-stock");
            if (item.val() > dbStock) {
                item.val(dbStock);
            } else if (item.val() < 1)
                item.val(1);
        });
    </script>
</body>

</html>