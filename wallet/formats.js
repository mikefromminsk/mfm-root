function addFormats($scope){
    function round(num, precision) {
        return +(Math.round(num + "e+" + precision) + "e-" + precision);
    }
    var numberFormat = new Intl.NumberFormat()
    $scope.priceFormat = function (number) {
        return "$" + numberFormat.format(round(number, 2))
    }
    $scope.amountFormat = function (number) {
        return round(number, 4) // K M B T
    }
    $scope.changeFormat = function (number) {
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