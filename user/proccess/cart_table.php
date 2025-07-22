<?php
//cart_table.php
require '../../config/db.php';
session_start();
$user_id = "1"; // to be changed later

if (isset($_POST['update_product_id']) && isset($_POST['new_stock_value'])) {

    $productId = mysqli_real_escape_string($conn, $_POST['update_product_id']);
    $newStock = mysqli_real_escape_string($conn, $_POST['new_stock_value']);

    $result = mysqli_query($conn, "UPDATE cart SET quantity = '$newStock' WHERE product_id = '$productId'");
    if ($result) {
        $_SESSION['message-status'] = "success";
        $_SESSION['message'] = "Updated Quantity";
    } else {
        http_response_code(500);
        $_SESSION['message-status'] = "fail";
        $_SESSION['message'] = "Error updating";
    }
    exit();
}

if (isset($_POST['delete_product_id'])) {

    $productId = mysqli_real_escape_string($conn, $_POST['delete_product_id']);

    $result = mysqli_query($conn, "DELETE FROM cart WHERE product_id = '$productId'");
    if ($result) {
        $_SESSION['message-status'] = "success";
        $_SESSION['message'] = "Successfully Deleted Item";
    } else {
        http_response_code(500);
        $_SESSION['message-status'] = "fail";
        $_SESSION['message'] = "Error deleting";
    }
    exit();
}


// $.get to show data in elemnets for #last-row
if (isset($_GET['get_cart_detail']) && $_GET['get_cart_detail']) {

    $newMarkedIds = $_GET['marked_ids'];

    $whereClause = "";

    if (!empty($newMarkedIds)) {
        $selected_cart_id = $newMarkedIds;
        $whereClause = " AND cart_id IN ($selected_cart_id) ";
    }

    $cartResultSQL = "
        SELECT
            c.user_id,
            SUM(c.quantity) AS total_cart_quantity,
            SUM(c.quantity * p.product_price) AS total_cart_price
        FROM
            cart c
        INNER JOIN
            products p ON c.product_id = p.product_id
        WHERE user_id = '$user_id'$whereClause
        GROUP BY
            c.user_id
            ";

    $cartDetailResult = mysqli_query($conn, $cartResultSQL);
    if (mysqli_num_rows($cartDetailResult) > 0) {

        $cartDetail = mysqli_fetch_assoc($cartDetailResult);
        if (isset($_GET['get_total_cart_quantity']) && $_GET['get_total_cart_quantity'])
            echo $cartDetail['total_cart_quantity'];
        else if (isset($_GET['get_total_cart_price']) && $_GET['get_total_cart_price'])
            echo $cartDetail['total_cart_price'];
        else
            echo "No Data found";

    }

    exit();
}

// to delete check marked items
if (isset($_POST['delete_selected_cart']) && $_POST['delete_selected_cart']) {

    $newMarkedIds = $_POST['marked_ids'];

    $deleteSQL = "DELETE FROM cart WHERE cart_id IN ($newMarkedIds)";
    $deleteResult = mysqli_query($conn, $deleteSQL);
    if ($deleteResult) {
        $_SESSION['message-status'] = "success";
        $_SESSION['message'] = "Updated Quantity";
    } else {
        http_response_code(500);
        $_SESSION['message-status'] = "fail";
        $_SESSION['message'] = "Error updating";
    }
    echo "$deleteSQL";
    exit();
}

// to delete check marked items
if (isset($_POST['delete_selected_cart']) && $_POST['delete_selected_cart']) {

    $newMarkedIds = $_POST['marked_ids'];

    // $deleteSQL = "DELETE FROM cart WHERE cart_id IN ($newMarkedIds)";
    // $deleteResult = mysqli_query($conn, $deleteSQL);
    // if ($deleteResult) {
    //     $_SESSION['message-status'] = "success";
    //     $_SESSION['message'] = "Updated Quantity";
    // } else {
    //     http_response_code(500);
    //     $_SESSION['message-status'] = "fail";
    //     $_SESSION['message'] = "Error updating";
    // }
    // echo "$deleteSQL";

    exit();
}


if (isset($_POST['show_data'])) {

    $cartResult = mysqli_query($conn, "SELECT * FROM cart_details WHERE user_id = $user_id");
    if (mysqli_num_rows($cartResult) > 0) {
        while ($cartRow = mysqli_fetch_assoc($cartResult)) {

            $cart_id = $cartRow['cart_id'];
            $product_id = $cartRow['product_id'];
            $selected_quantity = $cartRow['quantity'];

            $product_name = $cartRow['product_name'];
            $product_price = $cartRow['product_price'];
            $db_stock = $cartRow['stock'];
            $img_path = "../img/product/" . $cartRow['image'];

            $item_user = $cartRow['user_id'];

            ?>
            <tr data-product-id='<?php echo $product_id; ?>'>
                <td class="px-1 text-center">
                    <div>
                        <label class="text-white">
                            <input name="selection" class="transition-all duration-500 ease-in-out dark:hover:scale-110 w-4 h-4"
                                type="checkbox" data-cart-id="<?php echo $cart_id; ?>">
                        </label>
                    </div>
                </td>
                <td class="pl-2 w-24 h-24">
                    <img src="<?php echo $img_path; ?>" alt="<?php echo $img_db['image']; ?>" class="object-cover rounded-md">
                </td>
                <td class="p-2 font-medium text-gray-900">
                    <?php echo $product_name; ?>
                </td>
                <td class="text-sm text-gray-500 text-center">
                    Rs.<?php echo $product_price; ?>
                </td>
                <td class="whitespace-nowrap">
                    <div class="flex justify-center">
                        <input type="hidden" name="previous_quantity" value="<?php echo $selected_quantity ?>">
                        <input type="number" name="new_quantity" value="<?php echo $selected_quantity; ?>" min="0"
                            max="<?php echo $db_stock; ?>" class="w-12 py-1 border border-gray-300 rounded text-center">
                    </div>
                </td>
                <td class="text-sm text-gray-500 text-center">
                    Rs. <?php echo $selected_quantity * $product_price; ?>
                </td>
                <td class="whitespace-nowrap text-sm font-medium text-center">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <button type="submit" name="update_from_cart"
                        class="px-3 py-2 rounded-md text-blue-500 bg-blue-200 hover:bg-red-00 hover:text-white hover:bg-blue-500">
                        Update
                    </button>
                    <button type="submit" name="delete_from_cart"
                        class="px-3 py-2 rounded-md text-red-500 bg-red-200 hover:bg-red-00 hover:text-white hover:bg-red-500">
                        Delete
                    </button>
                </td>
            </tr>

            <?php
        }
        // this is to prind the last row datas
        echo "<tr id='last-row'>";

        $whereClause = "";
        if (isset($_POST['selected_cart_id'])) {
            $selected_cart_id = $_POST['selected_cart_id'];
            $whereClause .= " AND cart_id IN ($selected_cart_id) ";
        }

        $cartResultSQL = "
            SELECT
                c.user_id,
                SUM(c.quantity) AS total_cart_quantity,
                SUM(c.quantity * p.product_price) AS total_cart_price
            FROM
                cart c
            INNER JOIN
                products p ON c.product_id = p.product_id
            WHERE
                user_id = '$user_id'$whereClause
            GROUP BY
                c.user_id
                ";
        $cartDetailResult = mysqli_query($conn, $cartResultSQL);
        if (mysqli_num_rows($cartDetailResult) > 0) {

            $cartDetail = mysqli_fetch_assoc($cartDetailResult);
            ?>


            <td colspan="2" class="">
                <div class="ml-5">
                    <button type="submit" name="delete_marked"
                        class="px-3 py-2 my-3 mx-1 rounded-md text-gray-500 bg-gray-200 hover:bg-gray-00 hover:text-white hover:bg-gray-500">
                        Delete Marked
                    </button>
                    <input type="hidden" class="bg-blue-200 p-2" name="selected_cart_id">
                </div>
            </td>
            <td colspan="2" class="text-lg pr-3 text-right">
                Total
            </td>
            <td class="text-lg">
                <div class="flex flex-col justify-center items-center">
                    <span id="total_quantity"><?php echo $cartDetail['total_cart_quantity'] ?></span>
                    <span class="text-sm text-gray-400">Item</span>
                </div>
            </td>
            <td colspan="2" class="text-gray-700">
                <div class="flex flex-col -space-y-2">
                    <span id="total_price" class="text-lg font-bold mx-auto">Rs.
                        <?php echo $cartDetail['total_cart_price'] ?></span>
                    <a type="submit" id="procced_checkout" href="checkout.php"
                        class="px-3 py-2 my-3 mx-auto text-nowrap bg-blue-300 rounded-md text-green-500 bg-green-200 hover:bg-red-00 hover:text-white hover:bg-green-500">
                        Procced to Checkout
                    </a>
            </td>

            <?php
        }

    } else
        echo "<tr><td colspan='7' class='text-center p-2 text-gray-400'>- -- --- No Data Found --- -- -</td></tr>";

?>
<?php


} else {
    echo "<tr><td colspan='7' class='text-center p-2 text-gray-400'>- -- --- No Data Found --- -- -</td></tr>";
}
// }
?>





<?php
if (isset($_SESSION['message-status']) && isset($_SESSION['message'])) {
    ?>
    <script>
        setFlashMessage("<?php echo $_SESSION['message-status'] ?>", "<?php echo $_SESSION['message'] ?>");
    </script>
    <?php
}
unset($_SESSION['message-ststus']);
unset($_SESSION['message']);
?>