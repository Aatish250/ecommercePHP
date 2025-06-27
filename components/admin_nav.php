<nav class="relative top-0 left-0 w-full h-16 bg-white flex justify-between items-center px-4 shadow-2xs">
    <div class="text-2xl font-bold">LOGO</div>
    <div class="flex items-center space-x-4">
        <a id="nav-link" href="dashboard.php" class="text-gray-400 hover:text-slate-900 ">Dashboard</a>
        <a id="nav-link" href="inventory.php" class="text-gray-400 hover:text-slate-900">Inventory</a>
        <a id="nav-link" href="sales.php" class="text-gray-400 hover:text-slate-900">Sales</a>
        <a id="nav-link" href="orders.php" class="text-gray-400 hover:text-slate-900">Orders</a>
        <a id="nav-link" href="users.php" class="text-gray-400 hover:text-slate-900">Users</a>
        <a id="nav-link" href="../index.php" class="text-gray-400 hover:text-slate-900">Logout</a>
    </div>
</nav>

<script>
    function placeActive(idx) {
        let navLink = document.querySelectorAll('#nav-link');
        navLink[idx].classList.add('font-bold', 'text-gray-800', 'bg-slate-100', 'cursor-default');
    }
    placeActive(<?php echo $active_page; ?>);
</script>