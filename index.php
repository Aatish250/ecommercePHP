<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="src/output.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Login Page</title>
</head>

<body class="bg-slate-200"">
    <nav class=" absolute top-0 left-0 w-full bg-slate-100 flex justify-between items-center p-4 text-4xl">
    Logo
    </nav>
    <div class="container flex justify-center items-center h-screen">
        <form action="" class="bg-white py-4 px-8 rounded-lg w-90 flex flex-col gap-2 md:w-92">
            <div class="text-center text-2xl font-bold py-3">Login</div>
            <label for="email" class="text-gray-500 text-sm -mb-2">Email</label>
            <input type="email" id="email" name="email"
                class="p-2 rounded-md bg-white outline-none border-1 border-gray-300 focus:border-black placeholder:text-gray-400 placeholder:italic placeholder:text-sm"
                placeholder="email@example.com">
            <label for="password" class="text-gray-500 text-sm -mb-2 mt-2">Password</label>
            <input type="password" id="password" name="password"
                class="p-2 rounded-md bg-white outline-none border-1 border-gray-300 focus:border-black placeholder:text-gray-400 placeholder:italic placeholder:text-sm"
                placeholder="Password">
            <button type="submit" class="bg-indigo-500 p-2 rounded-md text-white mt-5">Login</button>
            <div class="mt-2 text-center text-gray-400">
                No account? <a href="" class="underline text-indigo-400 hover:text-white">Sign up</a>
            </div>
        </form>
    </div>
</body>

</html>