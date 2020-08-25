<?php
require_once '/var/www/camagru/templates/components/Header.php';
?>

<main id="profile" class="text-center h-full">
    <div id="general" class="pt-10 w-11/12 md:w-8/12 mx-auto text-left">
        <?php
        require_once('/var/www/camagru/templates/views/profile/General.php');
        ?>
    </div>
    <div id="password" class="py-10 w-11/12 md:w-8/12 mx-auto">
        <?php
        require_once('/var/www/camagru/templates/views/profile/Password.php');
        ?>
    </div>
</main>

<script type="text/javascript" src="templates/views/auth/commonJs/UserValidator.js"></script>
<script>
    let general = true;
    let password = false;

    document.getElementById("submitPasswordButton").addEventListener("click", function (event) {
        event.preventDefault()
    });
</script>

<style>
    #profile {
        background: linear-gradient(90deg, #00C9FF 0%, #92FE9D 100%);
    }
    button:focus {
        outline: 0 !important;
    }
</style>

<?php
require_once '/var/www/camagru/templates/components/Footer.php';
?>
