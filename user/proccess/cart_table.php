<?php
//cart_table.php
require '../../config/db.php';
session_start();

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

$cartResult = mysqli_query($conn, "SELECT * FROM cart");
if (mysqli_num_rows($cartResult) > 0) {
    while ($cartRow = mysqli_fetch_assoc($cartResult)) {

        $product_id = $cartRow['product_id'];
        $selected_quantity = $cartRow['quantity'];

        $result = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $product_id");
        $item = mysqli_fetch_assoc($result);

        $product_name = $item['product_name'];
        $product_price = $item['product_price'];
        $db_stock = $item['stock'];

        $img_id = $item['img_id'];

        $img_result = mysqli_query($conn, "SELECT * FROM image where img_id = $img_id");
        $img_db = mysqli_fetch_assoc($img_result);
        $img_path = "../img/product/" . $img_db['image'];

        ?>
        <tr data-product-id='<?php echo $product_id; ?>'>
            <td class="pl-3">
                <!-- From Uiverse.io by themrsami -->
                <div class="">
                    <label class="text-white">
                        <input name="selection" class="transition-all duration-500 ease-in-out dark:hover:scale-110 w-4 h-4"
                            type="checkbox">
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
    ?>
    <tr class="text-right">
        <td colspan="2" class="whitespace-nowrap text-sm font-medium text-center">
            <button type="submit" name="delete_marked"
                class="px-3 py-2 my-3 mx-1 rounded-md text-gray-500 bg-gray-200 hover:bg-gray-00 hover:text-white hover:bg-gray-500">
                Delete Marked
            </button>
        </td>
        <td colspan="3" class="text-lg">
            Total
        </td>
        <td class="text-lg text-gray-700">
            $1299.99
        </td>
        <td class="whitespace-nowrap text-sm font-medium text-center">
            <button type="submit" name="procced_checkout"
                class="px-3 py-2 my-3 mx-1 rounded-md text-green-500 bg-green-200 hover:bg-red-00 hover:text-white hover:bg-green-500">
                Procced to Checkout
            </button>
        </td>
    </tr>
    <?php
} else {
    echo "<tr><td colspan='7' class='text-center p-2 text-gray-400'>- -- --- No Data Found --- -- -</td></tr>";
}
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