function post(url, params, success) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", url);
    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
    xhr.onload = () => {
        if (xhr.readyState == 4 && xhr.status == 200) {
            success(JSON.parse(xhr.response))
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
        callback(response)
    })
}

function dataSend(path,
                  fromAddress,
                  toAddress,
                  password,
                  next_hash,
                  amount,
                  callback) {

    post(path, a, {
        path: path,
    }, function (response) {
        callback(response)
    })
}

function dataInfo(path, callback) {
    post("/data/get.php", {
        path: path,
    }, function (response) {
        callback(response)
    })
}

function walletLogged(app) {
    localStorage.getItem(app)
}

function walletPassword(app) {
    localStorage.getItem(app)
}

function walletNextHash(app) {

}