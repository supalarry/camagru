<?php
require_once '/var/www/camagru/templates/components/Header.php';
?>

<main class="flex flex-col mt-24 h-screen">
    <div class="text-center">
        <h1 class="text-3xl text-gray-900">Reset password request expired</h1>
        <h1 class="text-3xl text-gray-900">Or such request does not exist</h1>
        <h1 class="text-3xl text-gray-900"><span><a class="text-3xl text-blue-600 tracking-wider pt-8"
                                                    href="http://localhost:8098/resetPasswordRequest">Reset again</a></span>
        </h1>
    </div>
    <div class="mx-auto mt-8">
        <img class="w-64" src="templates/views/auth/img/Sad.png">
    </div>
</main>

<?php
require_once '/var/www/camagru/templates/components/Footer.php';
?>
