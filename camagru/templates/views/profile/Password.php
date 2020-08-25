<main class="text-center bg-white p-8 rounded-lg">
    <div id="editPassword">
        <img id="loadingIconPassword" class="hidden h-16 mx-auto mb-2" src='templates/views/auth/img/loading.svg'>
        <form>
            <h1 id="messagePassword" class="hidden text-md rounded-md mb-5 py-5 text-white"></h1>
            <input id="newPassword" type="password" class="text-2xl w-full outline-none px-2 py-1 mb-2 border-0 rounded-md"
                   placeholder="new password">
            <input id="newPasswordRepeat" type="password" class="text-2xl w-full outline-none px-2 py-1 mb-4 border-0 rounded-md"
                   placeholder="repeat new password">
            <button type="submit" id="submitPasswordButton" onclick="updatePassword()"
                    class="block mx-auto px-2 py-2 mt-5 px-5 text-gray-900 border-2 border-gray-900 rounded-md outline-none">
                Update
            </button>
        </form>
    </div>
</main>

<script>
    document.getElementById("submitPasswordButton").addEventListener("click", function (event) {
        event.preventDefault()
    });

    async function updatePassword() {
        const updatedUser = {};
        updatedUser.id = <?php echo $args['id']; ?>;
        const newPassword = document.getElementById('newPassword').value.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const newPasswordRepeat = document.getElementById('newPasswordRepeat').value.replace(/</g, "&lt;").replace(/>/g, "&gt;");

        if (!validPassword(newPassword) || !passwordsMatch(newPassword, newPasswordRepeat)) {
            document.getElementById('messagePassword').innerHTML = getErrorMessage();
            passwordMessageIsErrorStyle();
            clearErrors();
            return;
        }

        updatedUser.password = newPassword;

        document.getElementById('messagePassword').classList.add('hidden');
        document.getElementById('loadingIconPassword').classList.remove('hidden');
        const rawResponse = await fetch('http://localhost:8098/profile', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updatedUser)
        });
        document.getElementById('loadingIconPassword').classList.add('hidden');
        document.getElementById('messagePassword').classList.remove('hidden');

        const content = await rawResponse.json();

        if (content && content['errors']) {
            passwordMessageIsErrorStyle();
            document.getElementById('messagePassword').innerHTML = getErrorMessage(content['errors']);;
        } else if (content && content['message'] === 'success') {
            passwordMessageIsSuccessStyle();
            document.getElementById('messagePassword').innerHTML = "Success";
            document.getElementById('newPassword').value = '';
            document.getElementById('newPasswordRepeat').value = '';
            setTimeout(function () {
                clearPasswordMessageStyle();
                document.getElementById('messagePassword').innerHTML = '';
            }, 3000);
        } else {
            passwordMessageIsErrorStyle();
            document.getElementById('messagePassword').innerHTML = "Something went wrong. Please, try later.";
        }
    }

    function passwordMessageIsErrorStyle()
    {
        let messagePassword = document.getElementById('messagePassword');
        if (messagePassword.classList.contains('hidden')){
            messagePassword.classList.remove('hidden');
        }
        if (messagePassword.classList.contains('bg-green-500')){
            messagePassword.classList.remove('bg-green-500');
        }
        messagePassword.classList.add('bg-red-500');
    }

    function passwordMessageIsSuccessStyle()
    {
        let messagePassword = document.getElementById('messagePassword');
        if (messagePassword.classList.contains('hidden')){
            messagePassword.classList.remove('hidden');
        }
        if (messagePassword.classList.contains('bg-red-500')){
            messagePassword.classList.remove('bg-red-500');
        }
        messagePassword.classList.add('bg-green-500');
    }

    function clearPasswordMessageStyle()
    {
        let messagePassword = document.getElementById('messagePassword');
        if (messagePassword.classList.contains('bg-green-500')){
            messagePassword.classList.remove('bg-green-500');
        }
        if (messagePassword.classList.contains('bg-red-500')){
            messagePassword.classList.remove('bg-red-500');
        }
        messagePassword.classList.add('hidden');
    }

</script>
