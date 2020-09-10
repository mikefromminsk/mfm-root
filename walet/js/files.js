
function readfile(e) {
    let file = e.target.files[0]
    if (!file)
        return;
    let reader = new FileReader()
    reader.onload = function (e) {
        validate(JSON.parse(e.target.result))
    };
    reader.readAsText(file);
    document.getElementById("validate").value = "";
}