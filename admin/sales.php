<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../src/output.css" rel="stylesheet">
    <?php require '../components/link_imports.php'; ?>
    <title>Sales Report</title>
</head>

<body class="bg-slate-200">

    <?php
    $active_page = 2;
    include '../components/admin_nav.php';
    include '../components/flashMessage.php';
    ?>

    <script>
        // setFlashMesg("info", "Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam, assumenda.");
    </script>

    <section>
        <div class="container m-auto p-3">

            <h2 class="p-5 font-bold text-3xl text-blue-900">Sales Report</h2>
            <div class="flex flex-col flex-wrap md:flex-row">
                <!-- total sales -->
                <div
                    class="px-5 py-3 m-1 rounded-lg flex items-center bg-white max-md:justify-between max-md:items-center md:flex-1/5 lg:flex-1/5 shadow-sm">
                    <!-- <span><img class="w-8" src="../img/coins.svg" alt=""></span> -->
                    <span class="bg-green-200 p-3 rounded-full">
                        <svg class="w-6 h-auto fill-green-400" xmlns="http://www.w3.org/2000/svg" id="Layer_1"
                            data-name="Layer 1" viewBox="0 0 24 24" width="512" height="512">
                            <path
                                d="M12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm4-8c0,1.654-1.346,3-3,3v1c0,.553-.447,1-1,1s-1-.447-1-1v-1h-.268c-1.067,0-2.063-.574-2.598-1.499-.277-.479-.113-1.09,.364-1.366,.479-.279,1.091-.113,1.366,.364,.179,.31,.511,.501,.867,.501h2.268c.552,0,1-.448,1-1,0-.378-.271-.698-.644-.76l-3.041-.507c-1.342-.223-2.315-1.373-2.315-2.733,0-1.654,1.346-3,3-3v-1c0-.552,.447-1,1-1s1,.448,1,1v1h.268c1.067,0,2.063,.575,2.598,1.5,.277,.478,.113,1.089-.364,1.366-.48,.277-1.091,.113-1.366-.365-.179-.309-.511-.5-.867-.5h-2.268c-.552,0-1,.449-1,1,0,.378,.271,.698,.644,.76l3.041,.507c1.342,.223,2.315,1.373,2.315,2.733Z" />
                        </svg>
                    </span>
                    <div class="flex mb-1 md:flex-col w-full">
                        <span class="flex-1 ml-5 text-[18px] text-slate-700">Total Sales</span>
                        <span class="mr-5 ml-5 font-bold text-2xl text-slate-500">Rs. 1200</span>
                    </div>
                </div>
                <!-- Total order -->
                <div
                    class="px-5 py-3 m-1 rounded-lg flex items-center bg-white max-md:justify-between max-md:items-center md:flex-1/5 lg:flex-1/5 shadow-sm">
                    <!-- <span><img class="w-6" src="../img/shopping-cart.svg" alt=""></span> -->
                    <span class="bg-amber-100 p-3 rounded-full">
                        <svg class="w-6 h-auto fill-amber-400" xmlns="http://www.w3.org/2000/svg" id="Outline"
                            viewBox="0 0 24 24" width="512" height="512">
                            <path
                                d="M21,6H18A6,6,0,0,0,6,6H3A3,3,0,0,0,0,9V19a5.006,5.006,0,0,0,5,5H19a5.006,5.006,0,0,0,5-5V9A3,3,0,0,0,21,6ZM12,2a4,4,0,0,1,4,4H8A4,4,0,0,1,12,2ZM22,19a3,3,0,0,1-3,3H5a3,3,0,0,1-3-3V9A1,1,0,0,1,3,8H6v2a1,1,0,0,0,2,0V8h8v2a1,1,0,0,0,2,0V8h3a1,1,0,0,1,1,1Z" />
                        </svg>
                    </span>
                    <div class="flex mb-1 md:flex-col w-full">
                        <span class="flex-1 ml-5 text-[18px] text-slate-700">Total Orders</span>
                        <span class="mr-5 ml-5 font-bold text-2xl text-slate-500">2</span>
                    </div>
                </div>
                <!-- total products -->
                <div
                    class="px-5 py-3 m-1 rounded-lg flex items-center bg-white max-md:justify-between max-md:items-center md:flex-1/5 lg:flex-1/5 shadow-sm">
                    <!-- <span><img class="w-6" src="../img/apps.svg" alt=""></span> -->
                    <span class="bg-indigo-200 p-3 rounded-full">
                        <svg class="w-6 h-auto fill-indigo-400" xmlns="http://www.w3.org/2000/svg" id="Layer_1"
                            data-name="Layer 1" viewBox="0 0 24 24" width="512" height="512">
                            <path
                                d="M16,23c0,.552-.447,1-1,1H6c-2.757,0-5-2.243-5-5V5C1,2.243,3.243,0,6,0h4.515c1.869,0,3.627,.728,4.95,2.05l3.484,3.486c.271,.271,.523,.568,.748,.883,.321,.449,.217,1.074-.232,1.395-.449,.32-1.075,.217-1.395-.233-.161-.225-.341-.438-.534-.63l-3.485-3.486c-.318-.318-.671-.587-1.051-.805V7c0,.551,.448,1,1,1h3c.553,0,1,.448,1,1s-.447,1-1,1h-3c-1.654,0-3-1.346-3-3V2.023c-.16-.015-.322-.023-.485-.023H6c-1.654,0-3,1.346-3,3v14c0,1.654,1.346,3,3,3H15c.553,0,1,.448,1,1Zm5.685-6.733l-3.041-.507c-.373-.062-.644-.382-.644-.76,0-.551,.448-1,1-1h2.268c.356,0,.688,.192,.867,.5,.275,.478,.885,.641,1.366,.365,.478-.277,.642-.888,.364-1.366-.534-.925-1.53-1.5-2.598-1.5h-.268v-1c0-.552-.447-1-1-1s-1,.448-1,1v1c-1.654,0-3,1.346-3,3,0,1.36,.974,2.51,2.315,2.733l3.041,.507c.373,.062,.644,.382,.644,.76,0,.551-.448,1-1,1h-2.268c-.356,0-.688-.192-.867-.5-.275-.479-.886-.642-1.366-.365-.478,.277-.642,.888-.364,1.366,.534,.925,1.53,1.499,2.598,1.499h.268v1c0,.552,.447,1,1,1s1-.448,1-1v-1c1.654,0,3-1.346,3-3,0-1.36-.974-2.51-2.315-2.733Zm-14.185-1.267h5.5c.553,0,1-.448,1-1s-.447-1-1-1H7.5c-1.378,0-2.5,1.122-2.5,2.5v2c0,1.378,1.122,2.5,2.5,2.5h5.5c.553,0,1-.448,1-1s-.447-1-1-1H7.5c-.276,0-.5-.224-.5-.5v-2c0-.276,.224-.5,.5-.5Zm-1.5-4h2c.552,0,1-.448,1-1s-.448-1-1-1h-2c-.552,0-1,.448-1,1s.448,1,1,1Zm0-4h2c.552,0,1-.448,1-1s-.448-1-1-1h-2c-.552,0-1,.448-1,1s.448,1,1,1Z" />
                        </svg>
                    </span>
                    <div class="flex mb-1 md:flex-col w-full">
                        <span class="flex-1 ml-5 text-[18px] text-slate-700">Total Products</span>
                        <span class="mr-5 ml-5 font-bold text-2xl text-slate-500">100</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container mx-auto flex justify-between space-x-6 flex-col lg:flex-row mb-10 px-3">
        <!-- Daily Sales -->
        <div class="bg-white rounded-lg shadow-md p-6 mx-1 w-full">
            <div class="flex justify-between md:items-center mb-6 max-md:flex-col">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Daily Sales</h2>
                <form method="GET" class="flex items-end max-md:space-y-2 max-md:flex-col">
                    <div class="flex max-md:w-full">
                        <div class="max-md:flex-1/2">
                            <label class="block text-sm text-gray-700">Start Date</label>
                            <input type="date" name="start_date" value=""
                                class="p-1 border border-gray-300 rounded-md max-md:w-full mr-2">
                        </div>
                        <div class="max-md:flex-1/2">
                            <label class="block text-sm text-gray-700">End Date</label>
                            <input type="date" name="end_date" value=""
                                class="p-1 border border-gray-300 rounded-md max-md:w-full mr-2">
                        </div>
                    </div>
                    <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 w-full h-full">
                        Filter
                    </button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                Jun 21, 2025
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">1</td>
                            <td class="px-4 py-3 text-sm text-gray-900">$200</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                Jun 21, 2025
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">1</td>
                            <td class="px-4 py-3 text-sm text-gray-900">$200</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                Jun 21, 2025
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">1</td>
                            <td class="px-4 py-3 text-sm text-gray-900">$200</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>

</html>