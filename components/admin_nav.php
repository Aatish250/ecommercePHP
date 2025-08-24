<nav class="relative top-0 left-0 w-full h-16 bg-white flex justify-between items-center px-4 shadow-2xs">
    <div class="text-2xl font-bold">
        <img src="../img/logo/a2ztransparent.png" alt="Logo" class="h-30 p-2">
    </div>
    <div class="flex items-center space-x-4">
        <a id="nav-link" href="dashboard.php" class="text-gray-400 hover:text-slate-900 hover:bg-slate-100 py-2 px-3 rounded-2xl ">Dashboard</a>
        <a id="nav-link" href="inventory.php" class="text-gray-400 hover:text-slate-900 hover:bg-slate-100 py-2 px-3 rounded-2xl">Inventory</a>
        <a id="nav-link" href="sales.php" class="text-gray-400 hover:text-slate-900 hover:bg-slate-100 py-2 px-3 rounded-2xl">Sales</a>
        <a id="nav-link" href="orders.php" class="text-gray-400 hover:text-slate-900 hover:bg-slate-100 py-2 px-3 rounded-2xl">Orders</a>
        <a id="nav-link" href="users.php" class="text-gray-400 hover:text-slate-900 hover:bg-slate-100 py-2 px-3 rounded-2xl">Users</a>
        <form action="../">
            <button id="nav-link" name="logout" value="true" class="text-gray-400 hover:text-slate-900">Logout</button>
        </form>
    </div>
</nav>
<script>
    function placeActive(idx) {
        let navLink = document.querySelectorAll('#nav-link');
        navLink[idx].classList.add('font-bold', 'text-gray-800', 'bg-slate-200', 'cursor-default');
    }
    placeActive(<?php echo $active_page; ?>);
</script>