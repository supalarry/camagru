<?php
require_once '/var/www/camagru/templates/components/Header.php';
?>

<main class="h-auto pt-4 main pb-10">
    <section class="w-9/12 text-center max-w-sm p-5 mx-auto mt-6 bg-white rounded-md shadow-lg">
        <h1 class="text-md text-gray-900">Join camagru family</h1>
    </section>
    <section class="w-9/12 max-w-sm p-5 mx-auto mt-6 bg-white rounded-md shadow-lg">
        <div class="text-center">
            <img id="loadingIcon" class="hidden h-16 mx-auto mb-2" src='templates/views/auth/img/loading.svg'>
            <form>
                <h1 id="message" class="hidden text-md rounded-md mb-5 py-5 text-white"></h1>
                <input id="username" type="text" class="text-2xl w-full px-2 py-1 mt-4 mb-2 border-0 outline-none"
                       placeholder="username" required>
                <input id="email" type="email" class="text-2xl w-full px-2 py-1 mt-2 mb-2 border-0 outline-none"
                       placeholder="email" required>
                <input id="password" type="password" class="text-2xl w-full px-2 py-1 mt-2 mb-2 border-0 outline-none"
                       placeholder="password" required>
                <input id="passwordRepeat" type="password" class="text-2xl w-full px-2 py-1 mt-2 mb-4 border-0 outline-none"
                       placeholder="repeat password" required>
                <button type="submit" id="submitButton"
                        class="block mx-auto px-2 py-2 mt-5 px-5 text-gray-900 border-2 border-gray-900 rounded-md"
                        onclick="register()">Register
                </button>
            </form>
        </div>
    </section>
</main>

<script type="text/javascript" src="templates/views/auth/commonJs/UserValidator.js"></script>
<script type="text/javascript" src="templates/views/auth/commonJs/DisplayMessage.js"></script>
<script type="text/javascript">
    document.getElementById("submitButton").addEventListener("click", function (event) {
        event.preventDefault()
    });

    async function register() {
        const username = document.getElementById('username').value.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const email = document.getElementById('email').value.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const password = document.getElementById('password').value.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const passwordRepeat = document.getElementById('passwordRepeat').value.replace(/</g, "&lt;").replace(/>/g, "&gt;");

        if (!hasValidRegistratrationForm({username, email, password, passwordRepeat})) {
            document.getElementById('message').innerHTML = getErrorMessage();
            messageIsErrorStyle();
            clearErrors();
            return;
        }
        document.getElementById('message').classList.add('hidden');
        document.getElementById('loadingIcon').style.display = 'block';
        const rawResponse = await fetch('http://localhost:8098/register', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({username, email, password})
        });
        document.getElementById('loadingIcon').style.display = 'none';
        document.getElementById('message').classList.remove('hidden');
        const content = await rawResponse.json();

        if (content && content['errors']) {
            messageIsErrorStyle();
            document.getElementById('message').innerHTML = getErrorMessage(content['errors']);
        } else if (content && content['message'] === 'success') {
            messageIsSuccessStyle();
            document.getElementById('message').innerHTML = 'Success';
            setTimeout(function () {
                window.location.replace("http://localhost:8098/verify");
            }, 2000);
        } else {
            messageIsErrorStyle();
            document.getElementById('message').innerHTML = "Something went wrong. Please, try later.";
        }
    }
</script>

<style>
    .main {
        background: linear-gradient(90deg, #00C9FF 0%, #92FE9D 100%);
    }

    button:focus {
        outline: 0 !important;
    }

    body {
        background-color: black;
    }
</style>

<?php
require_once '/var/www/camagru/templates/components/Footer.php';
?>
