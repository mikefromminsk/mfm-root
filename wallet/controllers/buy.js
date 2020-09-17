app.controller('buy', function ($scope, $http) {
    $scope.amount
    $scope.amount_due
    $scope.buy_error = false
    $scope.buy_requesting = false

    $scope.$watch(function() {
        return $scope.amount;
    }, function(newValue, oldValue) {
        $scope.amount_due = Number((newValue * (1 - 0.02)).toFixed(2))
    });

    $scope.$watch(function() {
        return $scope.amount_due;
    }, function(newValue, oldValue) {
        $scope.amount = Number((newValue / (1 - 0.02)).toFixed(2))
    });

    $scope.buy = function () {
        $scope.login_requesting = true
        $http.post("api/action_insert.php", {
            action_amount: $scope.amount_due
        }, config).then(function (req) {
            $scope.buy_requesting = false
            if (req.data.action_id != null){
                document.getElementById("targets").value = "Транзакция " + req.data.action_id
                document.getElementById("label").value = req.data.action_id
                document.getElementById("amount").value = $scope.amount
                document.getElementById("go_yandex").submit()
            }
        }, function () {
            $scope.buy_error = true
            $scope.buy_requesting = false
        })
    }
});