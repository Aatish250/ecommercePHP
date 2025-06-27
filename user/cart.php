<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require '../components/link_imports.php'; ?>
    <title>Cart</title>
</head>

<body class="bg-slate-200">

    <?php

    session_start();
    $active_page = 1;
    include '../components/user_nav.php';
    include '../components/flashMessage.php';
    require '../config/db.php';
    ?>

    <section class="container mx-auto">
        <h2 class="p-5 font-bold text-3xl text-blue-900">Cart</h2>
        <div class="bg-white m-1 rounded-lg shadow-md">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th></th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Image</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="bg-white divide-y divide-gray-200">

                    </tbody>
                    <?php
                    // include 'proccess/cart_table.php';
                    ?>
                </table>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(() => {
            printData(true);

            $("#table-body").on('click', 'button[name="update_quantity"]', function () {
                alert("Clicked update_quantity")
            });

            $("#table-body").on('click', 'button[name="procced_checkout"]', function () {
                alert("Clicked procced-checkout")
            });

        });

        function printData(status) {
            if (status) {
                $("#table-body").load('proccess/cart_table.php', function () {
                    $("#table-body").find("button[name='update_quantity']").hide();
                });
            } else {
                $("#table-body").text("Error fetching data");
            }
        }

        // on stock change (show and hide updat btn)
        $("#table-body").on('change', "input[name='new_quantity']", function () {

            // console.log($(this).val());
            changeInput = $(this);
            row = changeInput.closest("tr");
            db_stock = row.find("input[name = 'previous_quantity']");
            updateBtn = row.find("button[name = 'update_quantity']");

            if (changeInput.val() != db_stock.val()) {
                updateBtn.show(300);
                // console.log("SHow");
            }
            else {
                updateBtn.hide(300);
                // console.log("hide");
            }
            // console.log(changeInput.val());
        });

        // -------- for delete button start
        // delete data
        $("#table-body").on('click', "button[name='delete_from_cart']", function () {
            const $clickButton = $(this);
            const $row = $clickButton.closest('tr');
            const productId = $row.data("product-id");
            // console.log(productId, newStock);

            $.ajax({
                url: 'proccess/cart_table.php',
                method: 'POST',
                data: {
                    delete_product_id: productId
                },
                success: function () {
                    printData(true);
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Delete Error:", status, error);
                    alert("Failed to delete product. Please try again.");
                    setFlashMessage("fail", "Error deleting");
                }
            });
        });
        // -------- for delete button end

        // -------- for update button start
        // update data
        $("#table-body").on('click', "button[name='update_from_cart']", function () {
            const $clickButton = $(this);
            const $row = $clickButton.closest('tr');
            const productId = $row.data("product-id");
            const newStock = $row.find("input[name='new_quantity']").val();
            // console.log(productId, newStock);

            $.ajax({
                url: 'proccess/cart_table.php',
                method: 'POST',
                data: {
                    update_product_id: productId,
                    new_stock_value: newStock
                },
                success: function () {
                    printData(true);
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Delete Error:", status, error);
                    alert("Failed to update product. Please try again.");
                    setFlashMessage("fail", "Error updating");
                }
            });
        });
        // -------- for update button end

    </script>
</body>

</html>