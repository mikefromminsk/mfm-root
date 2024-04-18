function openDeposit(success) {
    var depositCheckTimer
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/usdt/deposit/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.chain = "TRON"

            hasToken("usdt", function () {
                post("/usdt/api/deposit/start.php", {
                    address: wallet.address(),
                    chain: $scope.chain,
                }, function (response) {
                    $scope.deadline = response.deadline
                    $scope.deposit_address = response.deposit_address
                    startDepositCheckTimer()
                    $scope.$apply()
                }, function (response) {
                    showError(response.message)
                })
            })

            $scope.copy = function () {
                document.getElementById("deposit_address").focus();
                document.getElementById("deposit_address").select();
                document.execCommand("copy");
                showSuccess("Deposit address copied")
            }

            $scope.share = function () {
                navigator.share({
                    text: $scope.deposit_address,
                })
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
                        post("/usdt/api/deposit/check.php", {
                            deposit_address: $scope.deposit_address,
                            chain: $scope.chain,
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