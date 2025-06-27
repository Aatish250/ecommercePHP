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

    $active_page = 1;
    include '../components/user_nav.php';

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
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="pl-3">
                                <!-- From Uiverse.io by themrsami -->
                                <div class="">
                                    <label class="text-white">
                                        <input
                                            class="transition-all duration-500 ease-in-out dark:hover:scale-110 w-4 h-4"
                                            type="checkbox">
                                    </label>
                                </div>
                            </td>
                            <td class="pl-2 w-24 h-24">
                                <img src="https://picsum.photos/500" alt="https://picsum.photos/500"
                                    class="object-cover rounded-md">
                            </td>
                            <td class="p-2 font-medium text-gray-900">
                                Item #1
                            </td>
                            <td class="text-sm text-gray-500 text-center">
                                $1,299.99
                            </td>
                            <td class="whitespace-nowrap">
                                <div class="flex justify-center">
                                    <input type="number" name="new_stock" value="5" min="0"
                                        class="w-12 py-1 border border-gray-300 rounded text-center">
                                </div>
                            </td>
                            <td class="text-sm text-gray-500 text-center">
                                $1,299.99
                            </td>
                            <td class="whitespace-nowrap text-sm font-medium text-center">
                                <form method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    <input type="hidden" name="product_id" value="">
                                    <button type="submit" name="update_stock"
                                        class="px-3 py-2 rounded-md text-blue-500 bg-blue-200 hover:bg-red-00 hover:text-white hover:bg-blue-500">
                                        Update
                                    </button>
                                    <button type="submit" name="delete_product"
                                        class="px-3 py-2 rounded-md text-red-500 bg-red-200 hover:bg-red-00 hover:text-white hover:bg-red-500">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-3">
                                <!-- From Uiverse.io by themrsami -->
                                <div class="">
                                    <label class="text-white">
                                        <input
                                            class="transition-all duration-500 ease-in-out dark:hover:scale-110 w-4 h-4"
                                            type="checkbox">
                                    </label>
                                </div>
                            </td>
                            <td class="pl-2 w-24 h-24">
                                <img src="https://picsum.photos/500" alt="https://picsum.photos/500"
                                    class="object-cover rounded-md">
                            </td>
                            <td class="p-2 font-medium text-gray-900">
                                Item #1
                            </td>
                            <td class="text-sm text-gray-500 text-center">
                                $1,299.99
                            </td>
                            <td class="whitespace-nowrap">
                                <div class="flex justify-center">
                                    <input type="number" name="new_stock" value="5" min="0"
                                        class="w-12 py-1 border border-gray-300 rounded text-center">
                                </div>
                            </td>
                            <td class="text-sm text-gray-500 text-center">
                                $1,299.99
                            </td>
                            <td class="whitespace-nowrap text-sm font-medium text-center">
                                <form method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    <input type="hidden" name="product_id" value="">
                                    <button type="submit" name="update_stock"
                                        class="px-3 py-2 rounded-md text-blue-500 bg-blue-200 hover:bg-red-00 hover:text-white hover:bg-blue-500">
                                        Update
                                    </button>
                                    <button type="submit" name="delete_product"
                                        class="px-3 py-2 rounded-md text-red-500 bg-red-200 hover:bg-red-00 hover:text-white hover:bg-red-500">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="text-right">
                            <td colspan="2" class="whitespace-nowrap text-sm font-medium text-center">
                                <button type="submit" name="update_stock"
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
                                <button type="submit" name="update_stock"
                                    class="px-3 py-2 my-3 mx-1 rounded-md text-green-500 bg-green-200 hover:bg-red-00 hover:text-white hover:bg-green-500">
                                    Procced to Checkout
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

</body>

</html>