function forx(number, func) {
    var index = 0;
    while (number > 1) {
        if (number % 2 === 0) number = number / 2;
        else number = 3 * number + 1;
        func(number, ++index);
    }
}

function change() {
    var c = document.getElementById("myCanvas")
    var ctx = c.getContext("2d");
    var width = c.getAttribute("width")
    var height = c.getAttribute("height")
    ctx.clearRect(0, 0, width, height);
    ctx.beginPath();
    ctx.moveTo(0, height);
    var index = 1;
    var start = document.getElementById("number").value;
    var highCount = 0;
    while (true) {
        var max = 0;
        highCount = 0;
        forx(start, function (number) {
            max = Math.max(max, number);
        })
        let startLow = true;
        forx(start, function (number, index) {
            if (index < 15 && number > max / 2)
                startLow = false;
        })
        var more = false;
        forx(start, function (number, index) {
            if (number > max / 2) {
                if (more == false) {
                    more = true
                    highCount++;
                }
            } else {
                if (more)
                    more = false;
            }
        })
        if (highCount <= 3 && startLow) {
            break;
        }
        start++;
    }
    document.getElementById("number").value = start;
    forx(start, function (number) {
        ctx.lineTo(index++ * 10, height - number / max * height)
    })
    ctx.stroke();
}


