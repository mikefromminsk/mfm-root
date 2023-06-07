function post(url, params, success) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", url);
    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
    xhr.onload = () => {
        if (xhr.readyState == 4 && xhr.status == 201) {
            success(JSON.parse(xhr.responseText))
        } else {
            console.log(`Error: ${xhr.status}`)
        }
    };
    xhr.send(JSON.stringify(params))
}

function dataGet(path, callback) {
    post("/data/get.php", {
        path: path,
    }, function (response) {
        callback(response.data)
    })
}

function dataInfo(path, callback) {
    post("/data/get.php", {
        path: path,
    }, function (response) {
        callback(response.data)
    })
}