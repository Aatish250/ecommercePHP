<?php
// inventory_products.php
require '../../config/db.php';
// update_product_id
// new_stock_value
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();


if (isset($_POST['update_product_id']) && isset($_POST['new_stock_value'])) {

    $productId = mysqli_real_escape_string($conn, $_POST['update_product_id']);
    $newStock = mysqli_real_escape_string($conn, $_POST['new_stock_value']);

    $result = mysqli_query($conn, "UPDATE products SET stock = '$newStock' WHERE product_id = '$productId'");
    if ($result) {
        $_SESSION['message-status'] = "success";
        $_SESSION['message'] = "Successfully Updated product Id: $productId with stock: $newStock";
    } else {
        http_response_code(500);
        $_SESSION['message-status'] = "danger";
        $_SESSION['message'] = "Error updating";
    }
    exit();
}

if (isset($_POST['delete_product_id'])) {
    
    
    $productId = mysqli_real_escape_string($conn, $_POST['delete_product_id']);
    $result = mysqli_query($conn, "DELETE FROM products WHERE product_id = '$productId'");
    $image = ($res = $conn->query("SELECT image FROM products WHERE product_id = $productId")) ? $res->fetch_assoc() : [];
    $image_path = "../../img/product/".$image['image'];
    if ($result) {
        if(file_exists($image_path)){
            $_SESSION['message-status'] = "success";
            if(unlink($image_path)) 
                $_SESSION['message-status'] = "success";
            $_SESSION['message'] = "Successfully Deleted product Id: $productId";
        }else {
            $_SESSION['message-status'] = "fail";
             $_SESSION['message'] = "Image File Not Found";
        }
    } else {
        http_response_code(500);
        $_SESSION['message-status'] = "fail";
        $_SESSION['message'] = "Error deleting product";
    }
    exit();
}




$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {
        ?>

        <tr data-product-id="<?php echo $row['product_id'] ?>">
            <td class="pl-2 w-16 h-16">
                <img src="../img/product/<?php echo $row['image'] ?>" alt="../img/product/<?php echo $row['image'] ?>"
                    class="object-cover rounded-md">
            </td>
            <td class="p-2 font-medium text-gray-900">
                <div class="flex flex-col">
                    <?php
                    echo $row['product_name'];
                    if ($row['stock'] < 8)
                        echo "
                        <span class='block mt-1 px-2 py-1 w-fit bg-red-100 text-red-800 text-xs font-semibold rounded-full'>
                            Low Stock
                        </span>
                    ";
                    ?>
                </div>

            </td>
            <td class="text-sm text-gray-500 text-center">
                <?php echo $row['category'] ?>
            </td>
            <td class="">
                <div class="flex justify-center">
                    Rs. <?php echo $row['product_price'] ?>
                </div>
            </td>
            <td class="whitespace-nowrap">
                <div class="flex justify-center">
                    <input type="hidden" name="db_stock" value="<?php echo $row['stock'] ?>">
                    <input type="number" name="new_stock" value="<?php echo $row['stock'] ?>" min="0"
                        class="w-12 py-1 border border-gray-300 rounded text-center">
                </div>
            </td>
            <td class="whitespace-nowrap text-sm font-medium text-center">
                <button type="button" name="update_stock" value="<?php echo $row['product_id'] ?>"
                    class="px-3 py-2 rounded-md text-blue-500 bg-blue-200 hover:bg-red-00 hover:text-white hover:bg-blue-500">
                    Update
                </button>
                <button type="submit" name="delete_product"
                    class="px-3 py-2 rounded-md text-red-500 bg-red-200 hover:bg-red-00 hover:text-white hover:bg-red-500">
                    Delete
                </button>
            </td>
        </tr>

        <?php
    }
} else {
    echo "<td colspan='100%' class='text-center text-gray-500'>- -- --- No Data found --- -- -</td>";
}
?>

<?php
    include "../../components/show_flash_message.php";
?>