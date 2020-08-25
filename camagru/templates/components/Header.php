<?php

if (!isset($_SESSION)) {
    session_start();
}

?>

<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="camagru" description="School 42 project">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>camagru</title>
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="#" />
</head>
<body>
<header>
    <nav class="m-auto bg-black sm:flex sm:justify-between sm:items-center sm:py-5 sm:px-10">
        <div class="flex items-center justify-between px-5 py-3 sm:p-0">
            <div id="logo">
                <a href="/catalog" class="text-3xl">
                    <h1 class="font-mono font-bold text-white">camagru</h1>
                </a>
            </div>
            <div id="burger" class="sm:hidden">
                <button type="button" class="block text-gray-800 focus:outline-none" onclick="toggle()">
                    <svg class="w-8 h-8 text-white fill-current" viewBox="0 0 24 24">
                        <path class="inline-block" id="openIcon" fill-rule="evenodd"
                              d="M4 5h16a1 1 0 0 1 0 2H4a1 1 0 1 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2z"/>
                        <path class="hidden" id="closeIcon" fill-rule="evenodd"
                              d="M18.278 16.864a1 1 0 0 1-1.414 1.414l-4.829-4.828-4.828 4.828a1 1 0 0 1-1.414-1.414l4.828-4.829-4.828-4.828a1 1 0 0 1 1.414-1.414l4.829 4.828 4.828-4.828a1 1 0 1 1 1.414 1.414l-4.828 4.829 4.828 4.828z"/>
                    </svg>
                </button>
            </div>
        </div>
        <div id="links" class="hidden px-2 pb-5 text-center sm:p-0 sm:block sm:flex sm:items-center">
            <?php
            if (isset($_SESSION['id'])) {
                echo "<a href='/editor' class='block px-2 py-2 font-semibold text-white rounded-md hover:bg-gray-100 hover:text-gray-900 sm:px-5'>Editor</a>";
            }
            ?>
            <a href="/catalog"
               class="block px-2 py-2 font-semibold text-white rounded-md hover:bg-gray-100 hover:text-gray-900 sm:px-5">Catalog</a>
            <?php
            if (isset($_SESSION['id'])) {
                echo "<a href='/profile' class='block px-2 py-2 font-semibold text-white rounded-md hover:text-gray-900 hover:bg-gray-100 sm:px-5'>Profile</a>";
                echo "<a class='block px-2 py-2 font-semibold text-white rounded-md hover:text-gray-900 hover:bg-gray-100 sm:px-5 cursor-pointer' onclick='logout()'>Log Out</a>";
            } else {
                echo "<a href='/login' class='block px-2 py-2 font-semibold text-white rounded-md hover:bg-gray-100 hover:text-gray-900 sm:px-5'>Log In</a>";
                echo "<a href='/register' class='block px-2 py-2 mt-1 font-semibold text-white rounded-md hover:bg-gray-100 hover:text-gray-900 sm:mt-0 sm:px-5'>Register</a>";
            }
            ?>
        </div>
    </nav>
    <script>
        let isOpen = false;
        let loggedIn = false;

        function toggle() {
            isOpen = !isOpen;
            if (isOpen) {
                document.getElementById('openIcon').style.display = 'none';
                document.getElementById('closeIcon').style.display = 'inline-block';
                document.getElementById('links').style.display = 'block';
            } else {
                document.getElementById('openIcon').style.display = 'inline-block';
                document.getElementById('closeIcon').style.display = 'none';
                document.getElementById('links').style.display = null;
            }
        }

        async function logout() {
            const logoutDate = new Date();
            const rawResponse = await fetch('http://localhost:8098/logout', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({logoutDate})
            });
            window.location.replace("http://localhost:8098/catalog");
        }
    </script>
</header>
