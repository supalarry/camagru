<?php
require_once '/var/www/camagru/templates/components/Header.php';
?>

<main class="h-screen pt-4 lg:pt-16 main">
    <section class="w-9/12 text-center max-w-sm p-5 mx-auto mt-6 bg-white rounded-md shadow-lg">
        <h1 class="text-md text-gray-900">Reset your password</h1>
    </section>
    <section class="w-9/12 max-w-sm p-5 mx-auto mt-6 bg-white rounded-md shadow-lg">
        <div class="text-center">
            <img id="loadingIcon" class="hidden h-16 mx-auto mb-2" src='templates/views/auth/img/loading.svg'>
            <form>
                <h1 id="message" class="hidden text-md rounded-md mb-5 py-5 text-white"></h1>
                <input id="email" class="text-2xl w-full px-2 py-1 mb-2 border-0 outline-none rounded-md" type="text"
                       placeholder="email" required>
                <button type="submit" id="submitButton" onclick="resetPassword()"
                        class="block mx-auto px-2 py-2 mt-5 px-5 text-gray-900 border-2 border-gray-900 rounded-md">
                    Reset
                </button>
            </form>
        </div>
    </section>
</main>


<script type="text/javascript" src="templates/views/auth/commonJs/UserValidator.js"></script>
<script type="text/javascript" src="templates/views/auth/commonJs/DisplayMessage.js"></script>
<script>
    document.getElementById("submitButton").addEventListener("click", function (event) {
        event.preventDefault()
    });

    async function resetPassword() {
        const email = document.getElementById('email').value.replace(/</g, "&lt;").replace(/>/g, "&gt;");

        if (!validEmail(email)) {
            document.getElementById('message').innerHTML = getErrorMessage();
            messageIsErrorStyle();
            clearErrors();
            return;
        }

        document.getElementById('message').classList.add('hidden');
        document.getElementById('loadingIcon').style.display = 'block';
        const rawResponse = await fetch('http://localhost:8098/resetPasswordRequest', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({email})
        });
        document.getElementById('loadingIcon').style.display = 'none';
        document.getElementById('message').classList.remove('hidden');
        const content = await rawResponse.json();

        if (content && content['errors']) {
            messageIsErrorStyle();
            document.getElementById('message').innerHTML = getErrorMessage(content['errors']);
        } else if (content && content['message'] === 'success') {
            messageIsSuccessStyle();
            document.getElementById('message').innerHTML = 'Please check your email';
        } else {
            messageIsErrorStyle();
            document.getElementById('message').innerHTML = "Something went wrong. Please, try later.";
        }
    }
</script>

<style>
    .main {
        background-image: radial-gradient(circle farthest-corner at 14.2% 27.5%, rgba(104, 199, 255, 1) 0%, rgba(181, 126, 255, 1) 90%);
    }

    button:focus {
        outline: 0 !important;
    }
</style>

<?php
require_once '/var/www/camagru/templates/components/Footer.php';
?>
