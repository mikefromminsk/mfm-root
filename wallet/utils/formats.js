function addFormats($scope){
    function round(num, precision) {
        return +(Math.round(num + "e+" + precision) + "e-" + precision);
    }
    var numberFormat = new Intl.NumberFormat()
    $scope.formatPrice = function (number) {
        return "$" + numberFormat.format(round(number, 2))
    }
    $scope.formatAmount = function (number, domain) {
        if (number >= 1000000)
            return round(number / 1000000, 2) + "M"
        if (number >= 1000)
            return round(number / 1000, 2) + "K"
        return round(number, 4) + (" " + (domain || "").toUpperCase())
    }
    $scope.formatTicker = function (domain) {
        return (domain || "").toUpperCase()
    }
    $scope.formatPercent = function (number) {
        number = round(number, 0)
        if (number < 0)
            return "-" + number + "%";
        else if (number == 0)
            return "0%";
        else if (number > 0)
            return "+" + number + "%";
    }

    $scope.percentColor = function (number) {
        return {'green-text': number > 0, 'red-text': number < 0}
    }

    $scope.timeFormat = function (number) {
        return new Date(number * 1000).toLocaleString()
    }

    $scope.percentFormat = function (number) {
        return round(number, 0) + "%";
    }

    $scope.tickerFormat = function (ticker) {
        return ticker.toUpperCase()
    }
}