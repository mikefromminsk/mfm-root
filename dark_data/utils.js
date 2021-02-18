function hash256(message, success) {
    return crypto.subtle.digest('SHA-256', new TextEncoder().encode(message)).then(function (bytearray) {
        const hashArray = Array.from(new Uint8Array(bytearray))
        const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('')
        success(hashHex)
    })
}


function darkdb(path, pass) {

    function http(params, success, error) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/dark_data/rest.php", false)
        xhr.onload = function (e) {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    success(JSON.parse(xhr.responseText))
                } else {
                    error(JSON.parse(xhr.responseText))
                }
            }
        }
        xhr.onerror = function (e) {
            error(e)
        }
        xhr.send(JSON.stringify(params))
    }


    function updateProps(root, props) {
        for (let key in props)
            if (props.hasOwnProperty(key)) {
                let value = props[key]
                if (typeof value == "string")
                    root[key] = value
                if (typeof value == "number")
                    root[key] = value
                if (typeof value == "object") {
                    Object.defineProperty(root, key, {
                        get: function () {
                            let obj = {}
                            this[key] = obj
                            return get_prop(obj, root._path + "." + key)
                        }
                    })
                }
            }
    }


    function get_prop(root, path) {
        root._path = path

        http({path: root._path, level: 1}, function (data) {
            updateProps(root, data.data)
        })
        root.set = function (property, value) {
            http({path: root._path + "." + property, level: 1, data: value}, function (data) {
                updateProps(root, data.data)
            })
        }
        return root
    }

    var root = {}
    get_prop(root, path)
    return root
}


