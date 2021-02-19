function hash256(message, success) {
    crypto.subtle.digest('SHA-256', new TextEncoder().encode(message)).then(function (bytearray) {
        const hashArray = Array.from(new Uint8Array(bytearray))
        const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('')
        success(hashHex)
    })
}


function db(path, pass, success) {

    hash256(pass, function (hash) {
        success(get_prop({}, path, hash))
    })

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


    function updateProps(root, props, hash) {
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
                            return get_prop(obj, root._path + "." + key, hash)
                        }
                    })
                }
            }
    }


    function get_prop(root, path, hash) {
        root._path = path

        http({path: root._path, level: 1}, function (data) {
            return updateProps(root, data.data, hash)
        })
        root.set = function (property, value) {
            let set_obj = null
            http({path: root._path + "." + property, hash: hash, level: 1, data: value}, function (data) {
                set_obj =  updateProps(root, data.data, hash)
            })
            return set_obj
        }
        return root
    }
}


