var httpRequestBasePath = ""
var httpRequestHeaders = {}

function postString(url, params, success, error) {
    const xhr = new XMLHttpRequest()
    xhr.open("POST", httpRequestBasePath + url, true)
    if (httpRequestHeaders != null)
        for (let key in httpRequestHeaders)
            if (httpRequestHeaders.hasOwnProperty(key))
                xhr.setRequestHeader(key, httpRequestHeaders[key]);
    xhr.onload = function (e) {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                if (success != null)
                    success(xhr.response);
            } else {
                if (error != null)
                    error();
            }
        }
    };
    xhr.onerror = error
    xhr.send(JSON.stringify(params))
}


function post(url, params, success, error) {
    postString(url, params, function (data) {
        success(data !== "" ? JSON.parse(data) : null)
    }, error)
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