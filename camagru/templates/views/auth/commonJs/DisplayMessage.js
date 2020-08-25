function messageIsErrorStyle()
{
    let messagePassword = document.getElementById('message');
    if (messagePassword.classList.contains('hidden')){
        messagePassword.classList.remove('hidden');
    }
    if (messagePassword.classList.contains('bg-green-500')){
        messagePassword.classList.remove('bg-green-500');
    }
    messagePassword.classList.add('bg-red-500');
}

function messageIsSuccessStyle()
{
    let messagePassword = document.getElementById('message');
    if (messagePassword.classList.contains('hidden')){
        messagePassword.classList.remove('hidden');
    }
    if (messagePassword.classList.contains('bg-red-500')){
        messagePassword.classList.remove('bg-red-500');
    }
    messagePassword.classList.add('bg-green-500');
}

function clearMessageStyle()
{
    let messagePassword = document.getElementById('message');
    if (messagePassword.classList.contains('bg-green-500')){
        messagePassword.classList.remove('bg-green-500');
    }
    if (messagePassword.classList.contains('bg-red-500')){
        messagePassword.classList.remove('bg-red-500');
    }
    messagePassword.classList.add('hidden');
}
