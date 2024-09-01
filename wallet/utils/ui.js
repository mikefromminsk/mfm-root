function controller(callback) {
    let app = angular.module("App", ['ngMaterial', 'ngAnimate'])
    app.config(function ($mdThemingProvider) {
        $mdThemingProvider.disableTheming();
    })
    app.controller("Controller", callback)
}

function selectFile(success, accept) {
    var input = document.createElement('input')
    input.type = 'file'
    input.accept = accept || "*/*"
    input.onchange = e => {
        if (success != null)
            success(e.target.files[0])
    }
    input.click()
}

function objectToForm(data) {
    var formData = new FormData();
    angular.forEach(data, function (value, key) {
        formData.append(key, value);
    });
    return formData;
}

function downloadFile(uri) {
    var link = document.createElement("a");
    link.setAttribute('download', uri.split(/(\\|\/)/g).pop());
    link.href = uri;
    document.body.appendChild(link);
    link.click();
    link.remove();
}

function animateFocus(id) {
    document.getElementById(id).animate(
        [
            {transform: "translateY(0px)"},
            {transform: "translateY(1px)"},
            {transform: "translateY(-1px)"},
            {transform: "translateY(0px)"},
        ],
        {
            duration: 300,
            iterations: 5,
        },
    )
}

function setFocus(id) {
    setTimeout(function () {
        document.getElementById(id).focus()
    }, 500)
}

function showError(message, error) {
    if (error) {
        error(message)
    } else {
        if (window.$mdToast != null) {
            window.$mdToast.show(window.$mdToast.simple().toastClass('red-toast').textContent(message))
        } else {
            alert(message)
        }
    }
}

function showSuccess(message, success) {
    if (window.$mdToast != null) {
        window.$mdToast.show(window.$mdToast.simple().toastClass('green-toast').textContent(message))
    } else {
        alert(message)
    }
    if (success)
        success(message)
}