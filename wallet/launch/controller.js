function openLaunchDialog(domain, success) {
    window.$mdDialog.show({
        templateUrl: "/wallet/launch/index.html",
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            $scope.amount = 1000000
            if (DEBUG) {
                $scope.domain = "super"
            }

            $scope.selectedIndex = 0

            $scope.$watch('domain', function (newValue, oldValue) {
                if (newValue != newValue.toLowerCase())
                    $scope.domain = newValue.toLowerCase()
                if (newValue.match(new RegExp("\\W")))
                    $scope.domain = oldValue
                if (newValue.indexOf(' ') != -1)
                    $scope.domain = oldValue
            })

            $scope.next = function () {
                if ($scope.selectedIndex == 0) {
                    $scope.in_progress = true
                    post('/data/api/search.php', {
                        path: "wallet/info",
                        search_text: $scope.domain,
                    }, function (response) {
                        if (response.result.indexOf($scope.domain) == -1) {
                            $scope.selectedIndex += 1;
                            $scope.checkLaunch()
                        } else {
                            showError($scope.domain.toUpperCase() + " domain exists")
                        }
                        $scope.in_progress = false
                        $scope.$apply()
                    })
                } else if ($scope.selectedIndex == 1) {
                    $scope.checkLaunch()
                }
            }

            $scope.checkLaunch = function () {
                hasBalance(wallet.gas_domain, function () {
                    wallet.calcStartHash($scope.domain, function (next_hash) {
                        postContractWithGas("wallet", "api/launch.php", {
                            domain: $scope.domain,
                            address: wallet.address(),
                            next_hash: next_hash,
                            amount: 1000000,
                        }, function () {
                            storage.pushToArray(storageKeys.domains, $scope.domain)
                            $scope.startLaunching()
                        })
                    })
                })
            }


            $scope.stages = [
                "Upload contracts",
                "Initializing token",
                "Global registration",
            ]
            $scope.stageIndex = -1
            $scope.in_progress = false
            $scope.startLaunching = function () {
                $scope.in_progress = true
                $scope.stageIndex += 1
                $scope.$apply()
                setTimeout(function () {
                    if ($scope.stageIndex < $scope.stages.length - 1) {
                        $scope.startLaunching()
                    } else {
                        openTokenSettings($scope.domain, success)
                    }
                }, DEBUG ? 100 : 3000)
            }


        }
    }).then(function () {
        if (success)
            success()
    })
}
