<?php
require_once '/var/www/camagru/templates/components/Header.php';
?>

<main class="flex flex-col mt-24">
    <div class="text-center">
        <h1 class="text-3xl text-gray-900">Verification successful</h1>
        <h1 class="text-3xl text-gray-900">You can now <span><a class="text-3xl text-blue-600 tracking-wider pt-8"
                                                                href="http://localhost:8098/login">LOGIN</a></span></h1>
    </div>
    <div class="mx-auto mt-8">
        <img class="w-64" src="templates/views/auth/img/Happy.png">
    </div>
</main>

<?php
require_once '/var/www/camagru/templates/components/Footer.php';
?>
