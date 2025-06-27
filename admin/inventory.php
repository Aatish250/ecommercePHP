<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require '../components/link_imports.php'; ?>
    <title>Inv</title>
</head>

<body class="bg-slate-200">
    <?php
    session_start();
    $active_page = 1;
    include '../components/admin_nav.php';
    include '../components/flashMessage.php';

    ?>

    <div class="container m-auto p-3">
        <div class="flex justify-between items-center">
            <span class="text-indigo-900 text-3xl font-bold">Invenotry Management</span>
            <button id="add-product" class="bg-blue-500 px-3 py-2 rounded-md text-white">Add New Product</button>
        </div>

        <!-- Add Product Form -->
        <section>
            <div id="addProductForm" class="bg-white rounded-lg shadow-md p-6 mb-2 mx-1 mt-4 hidden">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Add New Product</h2>
                <form method="POST" enctype="multipart/form-data" action="proccess/add_product.php"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                        <input type="text" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <input type="text" name="category" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                        <input type="number" name="price" step="0.01" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                        <input type="number" name="stock" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="md:col-span-2 flex items-center">
                        <label class="block w-22 text-sm font-medium text-gray-700 mb-2">Image URL</label>
                        <input type="file" name="image"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="md:col-span-2 flex space-x-4">
                        <button type="submit" name="add_product" value="add"
                            class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                            Add Product
                        </button>
                        <button type="button" id="cancel-form" onclick="toggleAddForm()"
                            class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
            <script>
                $("#add-product").click(function () {
                    addFormToggle();
                });

                $("#cancel-form").click(function () {
                    addFormToggle();
                });

                function addFormToggle() {
                    $("#addProductForm").slideToggle({
                        duration: 400,
                        easing: 'swing',
                        complete: function () {
                            if ($("#addProductForm").is(":visible")) {
                                $("#add-product").text("Close");
                            } else {
                                $("#add-product").text("Add New Product");
                            }
                        }
                    });
                }
            </script>
        </section>


        <!-- inventory.php -->
        <section class="container m-auto px-3">
            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden p-2">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th colspan="2"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Product</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Price</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Stock</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>

                        <tbody id="table-body" class="bg-white divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <script>

            $(document).ready(() => {
                printData(true);
            });

            // -------- for delete button start
            // delete data
            $("#table-body").on('click', "button[name='delete_product']", function () {
                const $clickButton = $(this);
                const $row = $clickButton.closest('tr');
                const productId = $row.data("product-id");
                // console.log(productId, newStock);

                $.ajax({
                    url: 'proccess/inventory_products.php',
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
            $("#table-body").on('click', "button[name='update_stock']", function () {
                const $clickButton = $(this);
                const $row = $clickButton.closest('tr');
                const productId = $row.data("product-id");
                const newStock = $row.find("input[name='new_stock']").val();
                // console.log(productId, newStock);

                $.ajax({
                    url: 'proccess/inventory_products.php',
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
            
            // on stock change (show and hide updat btn)
            $("#table-body").on('change', "input[name='new_stock']", function () {

                // console.log($(this).val());
                changeInput = $(this);
                row = changeInput.closest("tr");
                db_stock = row.find("input[name = 'db_stock']");
                updateBtn = row.find("button[name = 'update_stock']");

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

            // -------- for update button end



            // load conented of tables
            function printData(status) {
                if (status) {
                    $("#table-body").load('proccess/inventory_products.php', function () {
                        $("#table-body").find("button[name='update_stock']").hide();
                    });
                } else {
                    $("#table-body").text("Error fetching data");
                }
            }

        </script>
</body>

</html>