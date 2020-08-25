let validatorErrorsObject = {};

function validUsername(username) {
    let valid = true;

    if (!username) {
        validatorErrorsObject.username = 'Username not set';
        valid = false;
    } else if (username === '') {
        validatorErrorsObject.username = 'Username empty';
        valid = false;
    }

    if (!valid) {
        return false;
    }

    if (!username.match(/^[a-zA-Z0-9]*$/)) {
        validatorErrorsObject.username = 'Invalid username';
        valid = false;
    }

    return valid;
}

function validEmail(email) {
    let valid = true;

    if (!email) {
        validatorErrorsObject.email = 'Email not set';
        valid = false;
    } else if (email === '') {
        validatorErrorsObject.email = 'Email empty';
        valid = false;
    }

    if (!valid) {
        return false;
    }

    if (!email.match(/\S+@\S+\.\S+/)) {
        validatorErrorsObject.email = 'Invalid email';
        valid = false;
    }

    return valid;
}

function validPassword(password) {
    let valid = true;

    if (!password) {
        validatorErrorsObject.password = 'Password not set';
        valid = false;
    } else if (password === '') {
        validatorErrorsObject.password = 'Password empty';
        valid = false;
    }

    if (!valid) {
        return false;
    }

    if (!password.match(/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,12}$/)) {
        validatorErrorsObject.password = 'Password must contain<br>1 lowercase letter<br>1 uppercase letter<br>1 number<br>1 special character (@#-_$%^&+=§!?)<br>8 to 12 characters';
        valid = false;
    }

    return valid;
}

function passwordsMatch(password, passwordRepeat) {
    let valid = true;

    if (password !== passwordRepeat) {
        validatorErrorsObject.password = 'Password and repeated password do not match';
        valid = false;
    }

    return valid;
}

function hasValidRegistratrationForm(user) {
    if (!validUsername(user.username) ||
        !validEmail(user.email) ||
        !validPassword(user.password) ||
        !passwordsMatch(user.password, user.passwordRepeat)
    ) {
        return false;
    }
    return true;
}

function hasValidLoginForm(user) {
    if (!validUsername(user.username) || !validPassword(user.password)) {
        return false;
    }
    return true;
}

function getErrorMessage(errorsObject = validatorErrorsObject) {
    let errorMessage = '';
    const errors = Object.values(errorsObject);
    for (let i = 0; i < errors.length; i++) {
        errorMessage += errors[i] + "<br>";
    }
    return errorMessage;
}

function clearErrors() {
    validatorErrorsObject = {};
}
