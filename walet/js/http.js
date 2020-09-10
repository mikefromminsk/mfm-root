function post(url, params, success, error) {
    const xhr = new XMLHttpRequest()
    xhr.open("POST", url, true)
    xhr.onload = function (e) {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                if (success != null)
                    success(JSON.parse(xhr.response));
            } else {
                if (error != null)
                    error();
            }
        }
    };
    xhr.onerror = error
    xhr.send(JSON.stringify(params))
}

function download(data, filename, type) {
    var file = new Blob([data], {type: type})
    var a = document.createElement("a")
    var url = URL.createObjectURL(file)
    a.href = url
    a.download = filename
    document.body.appendChild(a)
    a.click()
    setTimeout(function () {
        document.body.removeChild(a)
        window.URL.revokeObjectURL(url)
    }, 0)
}