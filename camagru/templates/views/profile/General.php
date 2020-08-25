<main class="bg-white p-8 rounded-lg">
    <div id="showGeneral">
        <div>
            <h1 class="leading-tight text-gray-900 text-2xl tracking-wider">username</h1>
            <h2 class="leading-tight text-xl opacity-50 break-words"><?php echo $args['username']; ?></h2>
        </div>
        <div class="mt-8">
            <h1 class="leading-tight text-gray-900 text-2xl my-2 tracking-wider">email</h1>
            <h2 class="leading-tight text-xl opacity-50 break-words"><?php echo $args['email']; ?></h2>
        </div>
        <div class="mt-8 mb-16">
            <h1 class="leading-tight text-gray-900 text-2xl my-2 tracking-wider">notify about comments</h1>
            <h2 class="leading-tight text-xl opacity-50"><?php if ($args['notifyAboutComments'] === 1) {
                    echo "yes";
                } else {
                    echo "no";
                } ?></h2>
        </div>
        <button type="submit" id="submitButton"
                class="block mx-auto px-2 py-2 mt-5 px-5 text-gray-900 border-2 border-gray-900 rounded-md outline-none"
                onclick="editGeneral()">Edit
        </button>
    </div>

    <div id="editGeneral" class="hidden">
        <h1 id="messageGeneral" class="text-center hidden text-md rounded-md mb-5 py-5 text-white"></h1>
        <img id="loadingIcon" class="hidden h-16 mx-auto mb-2" src='templates/views/auth/img/loading.svg'>
        <div>
            <h1 class="leading-tight text-gray-900 text-2xl tracking-wider">username</h1>
            <h2 class="leading-tight text-xl opacity-50"><input id="changedUsername" class="outline-none w-full" type="text"
                                                                value="<?php echo $args['username']; ?>"></h2>
        </div>
        <div class="mt-8">
            <h1 class="leading-tight text-gray-900 text-2xl my-2 tracking-wider">email</h1>
            <h2 class="leading-tight text-xl opacity-50"><input id="changedEmail" class="outline-none w-full" type="text"
                                                                value="<?php echo $args['email']; ?>"></h2>
        </div>
        <div class="mt-8 mb-16">
            <h1 class="leading-tight text-gray-900 text-2xl my-2 tracking-wider">notify about comments</h1>
            <h2 class="leading-tight text-xl opacity-50"><select class="outline-none" id="changedNotification" name="notificaiton"
                                                                 id="notificaiton" class="w-24">
                    <option value="1">yes</option>
                    <option value="0">no</option>
                </select></h2>
        </div>
        <div class="text-center">
            <button type="submit" id="submitButton"
                    class="mx-auto px-2 py-2 mt-5 px-5 text-gray-900 border-2 border-gray-900 rounded-md outline-none"
                    onclick="saveGeneral()">Save
            </button>
            <button type="submit" id="submitButton"
                    class="mx-auto px-2 py-2 mt-5 px-5 text-gray-900 border-2 border-gray-900 rounded-md outline-none"
                    onclick="cancelGeneral()">Cancel
            </button>
        </div>
    </div>
</main>

<script>
    document.getElementById("submitButton").addEventListener("click", function (event) {
        event.preventDefault()
    });

    document.getElementById('changedNotification').value = "<?php echo $args['notifyAboutComments']; ?>";

    function editGeneral() {
        document.getElementById('showGeneral').style.display = 'none';
        document.getElementById('editGeneral').style.display = 'block';
    }

    async function saveGeneral() {
        const updatedUser = {};

        updatedUser.id = <?php echo $args['id']; ?>;
        const username = "<?php echo $args['username']; ?>";
        const email = "<?php echo $args['email']; ?>";
        const notification = <?php echo $args['notifyAboutComments']; ?>;

        const newUsername = document.getElementById('changedUsername').value.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const newEmail = document.getElementById('changedEmail').value.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const newNotificationDropdown = document.getElementById('changedNotification');
        const newNotification = newNotificationDropdown.options[newNotificationDropdown.selectedIndex].value;

        if (newUsername !== username) {
            if (!validUsername(newUsername)) {
                document.getElementById('messageGeneral').innerHTML = getErrorMessage();
                generalMessageIsErrorStyle();
                clearErrors();
                return;
            }
            updatedUser.username = newUsername;
        }

        if (newEmail !== email) {
            if (!validEmail(newEmail)) {
                document.getElementById('messageGeneral').innerHTML = getErrorMessage();
                generalMessageIsErrorStyle();
                clearErrors();
                return;
            }
            updatedUser.email = newEmail;
        }

        if (parseInt(newNotification) !== notification) {
            updatedUser.notifyAboutComments = parseInt(newNotification);
        }

        if (Object.keys(updatedUser).length === 1) {
            document.getElementById('showGeneral').style.display = 'block';
            document.getElementById('editGeneral').style.display = 'none';
            return;
        }

        document.getElementById('messageGeneral').classList.add('hidden');
        document.getElementById('loadingIcon').style.display = 'block';
        const rawResponse = await fetch('http://localhost:8098/profile', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updatedUser)
        });
        document.getElementById('loadingIcon').style.display = 'none';
        document.getElementById('messageGeneral').classList.remove('hidden');

        const content = await rawResponse.json();

        if (content && content['errors']) {
            generalMessageIsErrorStyle();
            document.getElementById('messageGeneral').innerHTML = getErrorMessage(content['errors']);
        } else if (content && content['message'] === 'success') {
            location.reload();
        } else {
            generalMessageIsErrorStyle();
            document.getElementById('messageGeneral').innerHTML = "Something went wrong. Please, try later.";
        }
    }

    function cancelGeneral() {
        document.getElementById('showGeneral').style.display = 'block';
        document.getElementById('editGeneral').style.display = 'none';
    }

    function generalMessageIsErrorStyle()
    {
        let messagePassword = document.getElementById('messageGeneral');
        if (messagePassword.classList.contains('hidden')){
            messagePassword.classList.remove('hidden');
        }
        if (messagePassword.classList.contains('bg-green-500')){
            messagePassword.classList.remove('bg-green-500');
        }
        messagePassword.classList.add('bg-red-500');
    }

    function generalMessageIsSuccessStyle()
    {
        let messagePassword = document.getElementById('messageGeneral');
        if (messagePassword.classList.contains('hidden')){
            messagePassword.classList.remove('hidden');
        }
        if (messagePassword.classList.contains('bg-red-500')){
            messagePassword.classList.remove('bg-red-500');
        }
        messagePassword.classList.add('bg-green-500');
    }

    function clearGeneralMessageStyle()
    {
        let messagePassword = document.getElementById('messageGeneral');
        if (messagePassword.classList.contains('bg-green-500')){
            messagePassword.classList.remove('bg-green-500');
        }
        if (messagePassword.classList.contains('bg-red-500')){
            messagePassword.classList.remove('bg-red-500');
        }
        messagePassword.classList.add('hidden');
    }
</script>
