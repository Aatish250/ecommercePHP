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
    require '../config/verify_session.php';
    verify_user("admin", "../");
    $active_page = 1;
    include '../components/admin_nav.php';
    include '../components/flashMessage.php';

    ?>


    <div class="container m-auto ">
        <div class="flex justify-between items-center">
            <span class="text-indigo-900 text-3xl font-bold">Invenotry Management</span>
            <button id="add-product" class="bg-blue-500 px-3 py-2 my-3 rounded-md text-white">Add New Product</button>
        </div>

        <!-- Add Product Form -->
        <section class="container m-auto px-3">
            <div id="addProductForm" class="bg-white rounded-lg shadow-md p-6 mb-5 mx-2 hidden">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Add New Product</h2>
                <form method="POST" enctype="multipart/form-data" action="proccess/add_product.php"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                        <input type="text" name="name" required autocomplete="off" value="Product Name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <input type="text" name="category" required autocomplete="off" value="Category"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                        <input type="number" name="price" step="0.01" required autocomplete="off" value="100" placeholder="Price"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                        <input type="number" name="stock" required autocomplete="off" value="10" placeholder="Stock"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="md:col-span-2 flex flex-col md:flex-row md:space-x-6 space-y-4 md:space-y-0 items-start">
                        <!-- Image Upload & Preview (Left Side, Larger) -->
                        <div class="flex flex-col items-center md:order-1 order-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                            <div 
                                id="image-preview-container"
                                class="w-56 h-56 border border-gray-300 rounded-md flex items-center justify-center bg-gray-100 overflow-hidden relative cursor-pointer"
                                style="aspect-ratio: 1 / 1;"
                            >
                                <input 
                                    type="file" 
                                    name="image" 
                                    id="product-image-input"
                                    accept="image/*"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    style="z-index:2;"
                                    onchange="previewProductImage(event)"
                                >
                                <img 
                                    id="image-preview" 
                                    src="" 
                                    alt="Image Preview" 
                                    class="object-cover w-full h-full hidden"
                                    style="z-index:1;"
                                >
                                <span id="image-placeholder" class="text-gray-400 text-xs absolute">1:1 Preview</span>
                            </div>
                            <span class="text-xs text-gray-500 mt-2 cursor-pointer" id="click-to-select-file">Click image to select file</span>
                        </div>
                        <!-- Description (Right Side) -->
                        <div class="flex-1 md:order-2 order-1 w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="7" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">Description</textarea>
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
                        </div>
                    </div>
                    <script>
                        // Make both the image area and the "Click image to select file" text open the file dialog
                        document.addEventListener('DOMContentLoaded', function() {
                            const imagePreviewContainer = document.getElementById('image-preview-container');
                            const clickToSelectFile = document.getElementById('click-to-select-file');
                            const fileInput = document.getElementById('product-image-input');
                            const preview = document.getElementById('image-preview');
                            const placeholder = document.getElementById('image-placeholder');

                            imagePreviewContainer.addEventListener('click', function(e) {
                                // Only trigger if the click is not on the file input itself
                                if (e.target !== fileInput) {
                                    fileInput.click();
                                }
                            });

                            clickToSelectFile.addEventListener('click', function() {
                                fileInput.click();
                            });

                            // Drag and drop support for images from browser (including internet sources)
                            imagePreviewContainer.addEventListener('dragover', function(e) {
                                e.preventDefault();
                                imagePreviewContainer.classList.add('ring-2', 'ring-blue-400');
                            });

                            imagePreviewContainer.addEventListener('dragleave', function(e) {
                                e.preventDefault();
                                imagePreviewContainer.classList.remove('ring-2', 'ring-blue-400');
                            });

                            imagePreviewContainer.addEventListener('drop', function(e) {
                                e.preventDefault();
                                imagePreviewContainer.classList.remove('ring-2', 'ring-blue-400');
                                // Handle file drop
                                if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                                    // If a file is dropped (from local)
                                    fileInput.files = e.dataTransfer.files;
                                    previewProductImage({ target: fileInput });
                                } else if (e.dataTransfer.items && e.dataTransfer.items.length > 0) {
                                    // If an image is dragged from browser (internet source)
                                    for (let i = 0; i < e.dataTransfer.items.length; i++) {
                                        const item = e.dataTransfer.items[i];
                                        if (item.kind === 'string' && item.type === 'text/uri-list') {
                                            item.getAsString(function(url) {
                                                // Fetch the image as a blob, then create a File and set it to the input
                                                fetch(url)
                                                    .then(response => response.blob())
                                                    .then(blob => {
                                                        // Try to get the filename from the URL
                                                        let filename = url.split('/').pop().split('?')[0];
                                                        // If no extension, default to .png
                                                        if (!/\.(jpe?g|png|gif|bmp|webp)$/i.test(filename)) {
                                                            filename += '.png';
                                                        }
                                                        const file = new File([blob], filename, { type: blob.type });
                                                        // Create a DataTransfer to set the file input
                                                        const dt = new DataTransfer();
                                                        dt.items.add(file);
                                                        fileInput.files = dt.files;
                                                        previewProductImage({ target: fileInput });
                                                    })
                                                    .catch(() => {
                                                        alert('Failed to fetch image from the internet source.');
                                                    });
                                            });
                                            break;
                                        }
                                    }
                                }
                            });
                        });

                        function previewProductImage(event) {
                            const input = event.target;
                            const preview = document.getElementById('image-preview');
                            const placeholder = document.getElementById('image-placeholder');
                            if (input.files && input.files[0]) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    preview.src = e.target.result;
                                    preview.classList.remove('hidden');
                                    placeholder.style.display = 'none';
                                }
                                reader.readAsDataURL(input.files[0]);
                            } else {
                                preview.src = '';
                                preview.classList.add('hidden');
                                placeholder.style.display = '';
                            }
                        }
                    </script>
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
        <?php
        
            unset($_SESSION['add-message-status']); 
            unset($_SESSION['add-message']);
        ?>
</body>

</html>