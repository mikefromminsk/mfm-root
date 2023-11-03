function round(num, precision) {
    return +(Math.round(num + "e+" + precision) + "e-" + precision);
}

function download(filename, url) {
    var element = document.createElement('a')
    element.setAttribute('href', url)
    element.setAttribute('download', filename)
    document.body.appendChild(element)
    element.click()
    //document.body.removeChild(element);
}

function selectFile(contentType, callback) {
    let input = document.createElement('input')
    input.type = 'file'
    input.accept = contentType
    input.onchange = _ => {
        let files = Array.from(input.files)
        if (callback != null)
            callback(files[0])
    }
    input.click();
}

function selectFileData(contentType, callback){
    selectFile(contentType, function (file) {
        var reader = new FileReader();
        reader.onload = function(evt) {
            if(evt.target.readyState != 2) return;
            if(evt.target.error) {
                return;
            }
            callback(evt.target.result)
        };
        reader.readAsText(file);
    })
}

function objectToForm(data) {
    var formData = new FormData();
    angular.forEach(data, function (value, key) {
        formData.append(key, value);
    });
    return formData;
}

function strArray(prefix) {
    let values = []
    for (let key in str) {
        if (key.startsWith(prefix))
            values.push(str[key])
    }
    return values
}