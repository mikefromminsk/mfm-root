const DEBUG = location.hostname == "localhost"

let contract = {
    send: '34ddc7c1919738b872759f3bf31169c5',
    free_reg: '63aab45e9f08996695d2ddad5c8eac6a',
    reg: '0902ce671e53ba0e175d78adc436b3ad',
    drop: 'c160df2cdc5c96d6a9e9f61d01a47676',
    init: '772df88baecd34099df80f0e592a9bc7',
    ico_buy: 'd670072f06bf06183fb422b9c28f1d8b',
    ico_sell: '8d0a5b6afe2082197857d58faef59655',
    bonus_create: 'c15a06590129c3854558b5ec282ffdad',
    bonus_receive: '8ed91430a15c6a19477b83c4debd6c60',
    wallet: '7242feda3f24473a3f86d9bd886e4510',
}

function randomString(length) {
    let result = '';
    const characters = 'abcdefghijklmnopqrstuvwxyz';
    const charactersLength = characters.length;
    let counter = 0;
    while (counter < length) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
        counter += 1;
    }
    return result;
}

function post(url, params, success, error) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", url);
    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
    xhr.onload = () => {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                if (success)
                    success(JSON.parse(xhr.response))
            } else {
                try {
                    var response = JSON.parse(xhr.response)
                    window.showError(response.message, error)
                } catch (e) {
                    window.showError(xhr.responseText, error)
                }
            }
        }
    };
    xhr.send(JSON.stringify(params))
}

function postWithGas(url, params, success, error) {
    wallet.postWithGas(url, params, success, error)
}

function contractExist(domain, contractHash, success, error) {
    post("/wallet/api/contracts.php", {
        domain: domain
    }, function (response) {
        var script_path = response.contracts[contractHash]
        if (script_path != null) {
            if (success)
                success(script_path)
        } else {
            if (error)
                error()
        }
    }, error)
}

function postContract(domain, contractHash, params, success, error) {
    contractExist(domain, contractHash, function (script_path) {
        post("/" + script_path, params, success, error)
    }, error)
}

function postContractWithGas(domain, contractHash, params, success, error) {
    contractExist(domain, contractHash, function (script_path) {
        wallet.postWithGas("/" + script_path, params, success, error)
    }, error)
}

function postForm(url, params, success, error) {
    const xhr = new XMLHttpRequest()
    const formData = new FormData()
    xhr.open("POST", url, true);
    xhr.onreadystatechange = () => {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                if (success)
                    success(JSON.parse(xhr.response))
            } else {
                if (error)
                    error(JSON.parse(xhr.response))
            }
        }
    }
    for (var key of Object.keys(params))
        formData.append(key, params[key])
    xhr.send(formData)
}

function dataGet(path, callback) {
    post("/data/api/get.php", {
        path: path,
    }, function (response) {
        if (callback)
            callback(response)
    })
}

const storageKeys = {
    username: "STORE_USERNAME",
    password: "STORE_PASSWORD",
    domains: "STORE_DOMAINS",
    drops: "STORE_DROPS",
}

var wallet = {
    username: "",
    password: "",
    quote_domain: "usdt",
    gas_domain: "data",
    gas_path: "data/wallet",
    init: function () {
        wallet.username = storage.getString(storageKeys.username)
        wallet.password = storage.getString(storageKeys.password)
    },
    auth: function (success, error) {
        if (wallet.username == "" || wallet.password == "") {
            let username = storage.getString(storageKeys.username)
            let password = storage.getString(storageKeys.password)
            if ((username == "" || password == "") && window.loginFunction != null) {
                window.loginFunction(function (username, password) {
                    wallet.login(username, password, success)
                })
            } else if (username == "" || password == "") {
                username = prompt("Enter your username")
                password = prompt("Enter your password")
                wallet.login(username, password, success)
            } else {
                wallet.login(username, password, success)
            }
        } else {
            success(wallet.username, wallet.password)
        }
    },
    login: function (username, password, success, error) {
        if (!username || !password) {
            if (error)
                error()
        } else {
            post("/" + wallet.gas_domain + "/api/token/wallet.php", {
                address: username,
            }, function (response) {
                if (response.next_hash == md5(wallet.calcHash(wallet.gas_path, username, password, response.prev_key))) {
                    storage.setString(storageKeys.username, username)
                    storage.setString(storageKeys.password, password)
                    wallet.username = username
                    wallet.password = password
                    if (success)
                        success(wallet.username, wallet.password)
                } else {
                    showError("password invalid", error)
                }
            }, error)
        }
    },
    reg: function (username, password, success, error) {
        postContract(wallet.gas_domain, contract.free_reg, {
            address: username,
            next_hash: md5(wallet.calcHash(wallet.gas_path, username, password))
        }, function () {
            wallet.login(username, password, success, error)
        }, error)
    },
    logout: function () {
        wallet.username = null
        wallet.password = null
        storage.clear()
    },
    calcKey: function (path, success, error) {
        wallet.auth(function (username, password) {
            post("/data/api/get.php", {
                path: path + "/" + username + "/prev_key",
            }, function (prev_key) {
                if (success) {
                    var key = wallet.calcHash(path, username, password, prev_key)
                    var next_hash = md5(wallet.calcHash(path, username, password, key))
                    success(
                        key,
                        next_hash,
                        username,
                        password
                    )
                }
            }, error)
        }, error)
    },
    send: function (domain, to_address, amount, success, error) {
        wallet.calcKey(wallet.gas_path, function (gas_key, gas_next_hash, username, password) {
            wallet.calcKey(domain + "/wallet", function (key, hash, username, password) {
                if (domain == wallet.gas_domain) {
                    gas_next_hash = wallet.calcHash(wallet.gas_path, username, password, gas_key)
                    gas_key = password
                }
                postContract(domain, contract.send, {
                    from_address: username,
                    to_address: to_address,
                    password: key,
                    next_hash: hash,
                    amount: amount,
                    gas_address: username,
                    gas_key: gas_key,
                    gas_next_hash: gas_next_hash,
                }, success, error)
            }, error)
        }, error)
    },
    // rename to calcKey
    calcHash: function (wallet_path, username, password, prev_key) {
        return md5(wallet_path + username + password + (prev_key || ""))
    },
    calcStartKey: function (path) {
        return md5(path + wallet.username + wallet.password)
    },
    calcStartHash: function (path) {
        return md5(this.calcStartKey(path))
    },
    postWithGas: function (url, params, success, error) {
        wallet.calcKey(wallet.gas_path, function (key, hash, username) {
            params.gas_address = username
            params.gas_key = key
            params.gas_next_hash = hash
            post(url, params, success, error)
        }, error)
    },
}


var storage = {
    getString: function (key, def) {
        var value = new URLSearchParams(window.location.search).get(key)
        if ((value == null || value == "") && window.NativeAndroid != null) {
            value = window.NativeAndroid.getItem(key)
        } else if ((value == null || value == "") && localStorage != null) {
            value = localStorage.getItem(key)
        }
        if (value == null) value = ""
        if (value == "" && def != null)
            return def
        return value
    },
    setString: function (key, val) {
        if (window.NativeAndroid != null) {
            window.NativeAndroid.setItem(key, val)
        } else {
            localStorage.setItem(key, val)
        }
    },
    getObject: function (key, def) {
        return JSON.parse(storage.getString(key, JSON.stringify(def)))
    },
    setObject: function (key, obj) {
        storage.setString(key, JSON.stringify(obj))
    },
    getStringArray: function (key) {
        var string = this.getString(key)
        return string == null || string == "" ? [] : string.split(',')
    },
    pushToArray: function (key, value) {
        if (this.isArrayItemExist(key, value)) return;
        var array = this.getStringArray(key)
        array.push(value)
        this.setString(key, array.join(","))
    },
    removeFromArray: function (key, value) {
        if (!this.getStringArray(key, value)) return;
        var array = this.getStringArray(key)
        array.splice(array.indexOf(value), 1);
        this.setString(key, array.join(","))
    },
    isArrayItemExist: function (key, value) {
        return this.getStringArray(key).indexOf(value) != -1
    },
    clear: function () {
        if (window.NativeAndroid != null) {
            window.NativeAndroid.clear()
        } else {
            localStorage.clear()
        }
    }
}

var md5 = function (string) {
    function RotateLeft(lValue, iShiftBits) {
        return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
    }

    function AddUnsigned(lX, lY) {
        var lX4, lY4, lX8, lY8, lResult;
        lX8 = (lX & 0x80000000);
        lY8 = (lY & 0x80000000);
        lX4 = (lX & 0x40000000);
        lY4 = (lY & 0x40000000);
        lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
        if (lX4 & lY4) {
            return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
        }
        if (lX4 | lY4) {
            if (lResult & 0x40000000) {
                return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
            } else {
                return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
            }
        } else {
            return (lResult ^ lX8 ^ lY8);
        }
    }

    function F(x, y, z) {
        return (x & y) | ((~x) & z);
    }

    function G(x, y, z) {
        return (x & z) | (y & (~z));
    }

    function H(x, y, z) {
        return (x ^ y ^ z);
    }

    function I(x, y, z) {
        return (y ^ (x | (~z)));
    }

    function FF(a, b, c, d, x, s, ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    }

    function GG(a, b, c, d, x, s, ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    }

    function HH(a, b, c, d, x, s, ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    }

    function II(a, b, c, d, x, s, ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    }

    function ConvertToWordArray(string) {
        var lWordCount;
        var lMessageLength = string.length;
        var lNumberOfWords_temp1 = lMessageLength + 8;
        var lNumberOfWords_temp2 = (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64;
        var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
        var lWordArray = Array(lNumberOfWords - 1);
        var lBytePosition = 0;
        var lByteCount = 0;
        while (lByteCount < lMessageLength) {
            lWordCount = (lByteCount - (lByteCount % 4)) / 4;
            lBytePosition = (lByteCount % 4) * 8;
            lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount) << lBytePosition));
            lByteCount++;
        }
        lWordCount = (lByteCount - (lByteCount % 4)) / 4;
        lBytePosition = (lByteCount % 4) * 8;
        lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
        lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
        lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
        return lWordArray;
    }

    function WordToHex(lValue) {
        var WordToHexValue = "", WordToHexValue_temp = "", lByte, lCount;
        for (lCount = 0; lCount <= 3; lCount++) {
            lByte = (lValue >>> (lCount * 8)) & 255;
            WordToHexValue_temp = "0" + lByte.toString(16);
            WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length - 2, 2);
        }
        return WordToHexValue;
    }

    function Utf8Encode(string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";
        for (var n = 0; n < string.length; n++) {
            var c = string.charCodeAt(n);
            if (c < 128) {
                utftext += String.fromCharCode(c);
            } else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            } else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
        }
        return utftext;
    }

    var x = Array();
    var k, AA, BB, CC, DD, a, b, c, d;
    var S11 = 7, S12 = 12, S13 = 17, S14 = 22;
    var S21 = 5, S22 = 9, S23 = 14, S24 = 20;
    var S31 = 4, S32 = 11, S33 = 16, S34 = 23;
    var S41 = 6, S42 = 10, S43 = 15, S44 = 21;
    string = Utf8Encode(string);
    x = ConvertToWordArray(string);
    a = 0x67452301;
    b = 0xEFCDAB89;
    c = 0x98BADCFE;
    d = 0x10325476;
    for (k = 0; k < x.length; k += 16) {
        AA = a;
        BB = b;
        CC = c;
        DD = d;
        a = FF(a, b, c, d, x[k + 0], S11, 0xD76AA478);
        d = FF(d, a, b, c, x[k + 1], S12, 0xE8C7B756);
        c = FF(c, d, a, b, x[k + 2], S13, 0x242070DB);
        b = FF(b, c, d, a, x[k + 3], S14, 0xC1BDCEEE);
        a = FF(a, b, c, d, x[k + 4], S11, 0xF57C0FAF);
        d = FF(d, a, b, c, x[k + 5], S12, 0x4787C62A);
        c = FF(c, d, a, b, x[k + 6], S13, 0xA8304613);
        b = FF(b, c, d, a, x[k + 7], S14, 0xFD469501);
        a = FF(a, b, c, d, x[k + 8], S11, 0x698098D8);
        d = FF(d, a, b, c, x[k + 9], S12, 0x8B44F7AF);
        c = FF(c, d, a, b, x[k + 10], S13, 0xFFFF5BB1);
        b = FF(b, c, d, a, x[k + 11], S14, 0x895CD7BE);
        a = FF(a, b, c, d, x[k + 12], S11, 0x6B901122);
        d = FF(d, a, b, c, x[k + 13], S12, 0xFD987193);
        c = FF(c, d, a, b, x[k + 14], S13, 0xA679438E);
        b = FF(b, c, d, a, x[k + 15], S14, 0x49B40821);
        a = GG(a, b, c, d, x[k + 1], S21, 0xF61E2562);
        d = GG(d, a, b, c, x[k + 6], S22, 0xC040B340);
        c = GG(c, d, a, b, x[k + 11], S23, 0x265E5A51);
        b = GG(b, c, d, a, x[k + 0], S24, 0xE9B6C7AA);
        a = GG(a, b, c, d, x[k + 5], S21, 0xD62F105D);
        d = GG(d, a, b, c, x[k + 10], S22, 0x2441453);
        c = GG(c, d, a, b, x[k + 15], S23, 0xD8A1E681);
        b = GG(b, c, d, a, x[k + 4], S24, 0xE7D3FBC8);
        a = GG(a, b, c, d, x[k + 9], S21, 0x21E1CDE6);
        d = GG(d, a, b, c, x[k + 14], S22, 0xC33707D6);
        c = GG(c, d, a, b, x[k + 3], S23, 0xF4D50D87);
        b = GG(b, c, d, a, x[k + 8], S24, 0x455A14ED);
        a = GG(a, b, c, d, x[k + 13], S21, 0xA9E3E905);
        d = GG(d, a, b, c, x[k + 2], S22, 0xFCEFA3F8);
        c = GG(c, d, a, b, x[k + 7], S23, 0x676F02D9);
        b = GG(b, c, d, a, x[k + 12], S24, 0x8D2A4C8A);
        a = HH(a, b, c, d, x[k + 5], S31, 0xFFFA3942);
        d = HH(d, a, b, c, x[k + 8], S32, 0x8771F681);
        c = HH(c, d, a, b, x[k + 11], S33, 0x6D9D6122);
        b = HH(b, c, d, a, x[k + 14], S34, 0xFDE5380C);
        a = HH(a, b, c, d, x[k + 1], S31, 0xA4BEEA44);
        d = HH(d, a, b, c, x[k + 4], S32, 0x4BDECFA9);
        c = HH(c, d, a, b, x[k + 7], S33, 0xF6BB4B60);
        b = HH(b, c, d, a, x[k + 10], S34, 0xBEBFBC70);
        a = HH(a, b, c, d, x[k + 13], S31, 0x289B7EC6);
        d = HH(d, a, b, c, x[k + 0], S32, 0xEAA127FA);
        c = HH(c, d, a, b, x[k + 3], S33, 0xD4EF3085);
        b = HH(b, c, d, a, x[k + 6], S34, 0x4881D05);
        a = HH(a, b, c, d, x[k + 9], S31, 0xD9D4D039);
        d = HH(d, a, b, c, x[k + 12], S32, 0xE6DB99E5);
        c = HH(c, d, a, b, x[k + 15], S33, 0x1FA27CF8);
        b = HH(b, c, d, a, x[k + 2], S34, 0xC4AC5665);
        a = II(a, b, c, d, x[k + 0], S41, 0xF4292244);
        d = II(d, a, b, c, x[k + 7], S42, 0x432AFF97);
        c = II(c, d, a, b, x[k + 14], S43, 0xAB9423A7);
        b = II(b, c, d, a, x[k + 5], S44, 0xFC93A039);
        a = II(a, b, c, d, x[k + 12], S41, 0x655B59C3);
        d = II(d, a, b, c, x[k + 3], S42, 0x8F0CCC92);
        c = II(c, d, a, b, x[k + 10], S43, 0xFFEFF47D);
        b = II(b, c, d, a, x[k + 1], S44, 0x85845DD1);
        a = II(a, b, c, d, x[k + 8], S41, 0x6FA87E4F);
        d = II(d, a, b, c, x[k + 15], S42, 0xFE2CE6E0);
        c = II(c, d, a, b, x[k + 6], S43, 0xA3014314);
        b = II(b, c, d, a, x[k + 13], S44, 0x4E0811A1);
        a = II(a, b, c, d, x[k + 4], S41, 0xF7537E82);
        d = II(d, a, b, c, x[k + 11], S42, 0xBD3AF235);
        c = II(c, d, a, b, x[k + 2], S43, 0x2AD7D2BB);
        b = II(b, c, d, a, x[k + 9], S44, 0xEB86D391);
        a = AddUnsigned(a, AA);
        b = AddUnsigned(b, BB);
        c = AddUnsigned(c, CC);
        d = AddUnsigned(d, DD);
    }
    var temp = WordToHex(a) + WordToHex(b) + WordToHex(c) + WordToHex(d);
    return temp.toLowerCase();
}


wallet.init()