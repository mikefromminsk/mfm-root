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
    $scope.formatPrice = function (number) {
        return "$" + shortNumber(number)
    }
    $scope.formatAmount = function (number, domain) {
        var result = shortNumber(number)
        if (domain != null)
            return result + " " + domain.toUpperCase()
        return result
    }
    $scope.formatTicker = function (domain) {
        return (domain || "").toUpperCase()
    }
    $scope.formatPercent = function (number) {
        if (number === undefined) return ""
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
            return {'gray-text': true}
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

    $scope.tickerFormat = function (ticker) {
        return ticker.toUpperCase()
    }
}