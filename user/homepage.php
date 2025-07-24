<!DOCTYPE html>
<html lang="en">
<!-- homepage.php -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require '../components/link_imports.php' ?>
    <title>Homepage</title>
</head>

<style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        /* Show up to 3 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<body class="bg-slate-200">

    <?php
    session_start();
    $active_page = 0;
    if(!isset($_SESSION['role']) && !isset($_SESSION['user_id'])){
        echo "No session";
        $_SESSION['role'] = "Guest";
        $_SESSION['user_id'] = "";
    }
    include '../components/user_nav.php';
    include '../components/flashMessage.php';
    
    ?>


    <section class="mx-auto">
        <!-- filters -->
        <div class="flex flex-col m-1 mt-4">
            <div class="bg-white p-2 m-1 rounded-md shadow-md flex md:space-x-3 max-md:flex-col max-md:space-y-2">
                <input type="text" id="search-bar"
                    value="<?php echo isset($_SESSION['previous-search']) ? $_SESSION['previous-search'] : ''; ?>"
                    class="outline-1 outline-gray-300 rounded-lg w-full p-3"
                    placeholder="Search by customer name, email, or order ID...">
                <div class="flex space-x-2">
                    <select id="category-select"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none flex-1">
                        <!-- all category are shown through jquery -->
                        <option></option>
                    </select>
                    <button class="bg-blue-500 text-white p-2 px-4 rounded-md">Search</button>
                </div>
            </div>
            <script>

            </script>
    </section>
    <?php
    // echo isset($_SESSION['previous-search']) ? $_SESSION['previous-search'] : 'no precious-search';
    // echo isset($_SESSION['previous-category']) ? $_SESSION['previous-category'] : 'no precious-category';
    // echo isset($_SESSION['message-status']) ? $_SESSION['message-status'] : 'no precious-status';
    ?>
    <section class="mx-auto">
        <!-- product grids -->
        <div class='mt-2 mx-15 md:mx-2 gap-5 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5'
            id="product-grid">
        </div>

    </section>

</body>

<script>
    function printData(status) {

        if (status) {
            $category = $("#category-select").val();
            $search = $("#search-bar").val();
            console.log("fetching data category:" + $category);
            console.log("fetching data Search:" + $search);

            $("#product-grid").load('proccess/home_products.php', { showData: status, category: $category, search: $search });
        }

        <?php
        if (isset($_SESSION['message-status']) && isset($_SESSION['message'])) {
            echo "setFlashMessage('" . $_SESSION['message-status'] . "','" . $_SESSION['message'] . "')";
            unset($_SESSION['message-status'],$_SESSION['message']);
        }
        ?>
    }

    $(document).ready(function () {

        // load all the catgory options
        $previousCategory = '<?php echo htmlspecialchars(isset($_SESSION['previous-category'])) ? htmlspecialchars($_SESSION['previous-category']) : ''; ?>';
        $("#category-select").load('proccess/home_products.php', { showOption: true, previousCategory: $previousCategory }, function () {
            console.log("Category options loaded. Triggering initial product grid load.");
            // After categories are loaded and selected, then load the product grid
            printData(true);
        });

        // feature to search data dinamically
        $category = $("#category-select").val();
        $search = $("#search-bar").val();


        $("#category-select").on('change', function () {
            printData(true);
        });
        $("#search-bar").on('keyup', function () {
            printData(true);
        });

    });
</script>

</html>