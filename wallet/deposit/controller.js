function openDeposit(success) {
    var depositCheckTimer
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/deposit/index.html',
        controller: function ($scope) {
            addFormats($scope)

            hasToken("usdt", function () {
                post("/usdt/api/deposit_start.php", {
                    address: wallet.address(),
                    chain: "BSC",
                }, function (response) {
                    $scope.deadline = response.deadline
                    $scope.deposit_address = response.deposit_address
                    startDepositCheckTimer()
                    $scope.$apply()
                }, function (response) {
                    showError(response.message)
                })
            })

            $scope.clear = function () {
                post("usdt/api/clear.php", {}, function () {
                    showSuccess("success")
                })
            }

            $scope.copy = function () {
                document.getElementById("deposit_address").focus();
                document.getElementById("deposit_address").select();
                document.execCommand("copy");
                showSuccess("Deposit address copied")
            }

            var CHECK_INTERVAL = 10
            $scope.countDownTimer = 0
            $scope.deposited = 0

            function startDepositCheckTimer() {
                $scope.countDownTimer = CHECK_INTERVAL
                depositCheckTimer = setInterval(function () {
                    $scope.countDownTimer -= 1
                    $scope.$apply()
                    if ($scope.countDownTimer % CHECK_INTERVAL == 0) {
                        $scope.countDownTimer = CHECK_INTERVAL
                        post("/usdt/api/deposit_check.php", {
                            deposit_address: $scope.deposit_address,
                            chain: "BSC",
                        }, function (response) {
                            if (response.deposited > 0){
                                $scope.back()
                                showSuccessDialog("You deposited " + $scope.formatAmount(response.deposited, "USDT"))
                            }
                        })
                    }
                }, 1000)
            }
        }
    }).then(function () {
        if (depositCheckTimer)
            clearInterval(depositCheckTimer)
        if (success)
            success()
    })
}