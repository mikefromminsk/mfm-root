function addFormats($scope) {
    $scope.round = function (num, precision) {
        return +(Math.round(num + "e+" + precision) + "e-" + precision)
    }

    function shortNumber(number) {
        number = $scope.round(number, 2)
        var numberFormat = new Intl.NumberFormat()
        var result
        if (number >= 1000000)
            result = numberFormat.format($scope.round(number / 1000000, 2)) + "M"
        else if (number >= 1000)
            result = numberFormat.format($scope.round(number / 1000, 2)) + "K"
        else
            result = numberFormat.format($scope.round(number, 4))
        return result
    }

    $scope.formatSec = function (sec) {
        var d = new Date(sec * 1000);
        function format_two_digits(n) {
            return n < 10 ? '0' + n : n;
        }
        var hours = format_two_digits(d.getHours());
        var minutes = format_two_digits(d.getMinutes());
        return hours + ":" + minutes;
    }

    $scope.formatPrice = function (number) {
        if (number == null)
            number = 0;
        return "$" + shortNumber(number)
    }
    $scope.formatAmount = function (number, domain) {
        if (number == null)
            number = 0;
        var result = shortNumber(number)
        if (domain != null) {
            if (domain.length > 5)
                domain = domain.substr(0, 3)
            return result + " " + domain.toUpperCase()
        }
        return result
    }
    $scope.formatDomain = function (domain) {
        if (domain == null) return ""
        if (domain.length > 5)
            domain = domain.substr(0, 3)
        return domain
    }
    $scope.formatTicker = function (domain) {
        return (domain || "").toUpperCase()
    }
    $scope.formatPercent = function (number) {
        if (number == null)
            number = 0;
        if (number == 0) return "0%";
        number = $scope.round(number, 0)
        if (number < 0)
            return "-" + number + "%";
        else if (number > 0)
            return "+" + number + "%";
    }

    $scope.percentColor = function (number) {
        if (number === undefined) return ""
        if (number == 0)
            return {'gray400-text': true}
        if (number > 0)
            return {'green-text': true}
        if (number < 0)
            return {'red-text': true}
    }

    $scope.timeFormat = function (number) {
        return new Date(number * 1000).toLocaleString()
    }

    $scope.percentFormat = function (number) {
        return $scope.round(number, 0) + "%";
    }

    $scope.formatTicker = function (ticker) {
        return ticker.toUpperCase()
    }

    $scope.genLogo = function (logo) {
        if (logo == null) return ""

        function getColor(t) {
            return "#" + t.slice(-6)
        }

        let canvas = document.createElement("canvas")
        canvas.width = 32
        canvas.height = 32
        let wh = canvas.height / 5
        let context = canvas.getContext("2d")
        let o = getColor(logo);
        context.clearRect(0, 0, canvas.width, canvas.height);
        for (let t = 0; t < 5; t++)
            for (let n = 0; n < 5; n++) {
                context.fillStyle = "transparent"
                context.moveTo(t + wh * n, wh * (n + 1))
                parseInt(logo.charAt(3 * t + (n > 2 ? 4 - n : n)), 16) % 2 && (context.fillStyle = o)
                context.fillRect(wh * n, wh * t, wh, wh)
                context.stroke()
            }
        return canvas.toDataURL()
    }

    $scope.back = function () {
        window.$mdBottomSheet.hide()
    }

    $scope.close = function () {
        window.$mdDialog.hide()
    }
}