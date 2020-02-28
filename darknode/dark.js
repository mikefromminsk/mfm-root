const $dark = {
    post: function (server_url, params, success, error) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", server_url, true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {//Вызывает функцию при смене состояния.
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200 && success != null)
                    success(xhr.response);
                if (xhr.status !== 200 && error != null)
                    error();
            }
        }
        xhr.send(params);
    },
    login: function () {

    },
    registration: function () {

    },
};