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

            $scope.category = Object.keys(tokenCategories)[0]

            $scope.categories = tokenCategories
            $scope.selectCategory = function (key) {
                $scope.category = key
            }

            $scope.next = function () {
                if ($scope.selectedIndex == 0) {
                    $scope.in_progress = true
                    post('/data/api/search.php', {
                        path: "wallet/info",
                        search_text: $scope.domain,
                    }, function (response) {
                        if (response.result.indexOf($scope.domain) == -1){
                            $scope.selectedIndex += 1;
                        } else {
                            showError($scope.domain.toUpperCase() + " domain exists")
                        }
                        $scope.in_progress = false
                        $scope.$apply()
                    })
                } else if ($scope.selectedIndex == 1) {
                    $scope.selectedIndex += 1;
                } else if ($scope.selectedIndex == 2) {
                    $scope.selectedIndex += 1;
                } else {
                    hasBalance(wallet.gas_domain, function () {
                        postWithGas("/wallet/api/launch.php", {
                            domain: $scope.domain,
                            address: wallet.username,
                            logo: $scope.logo,
                            category: $scope.category,
                            next_hash: wallet.calcStartHash($scope.domain + "/wallet"),
                            amount: 1000000,
                        }, function () {
                            storage.pushToArray(storageKeys.domains, $scope.domain)
                            $scope.startLaunching()
                        })
                    })
                }
            }

            $scope.generate = async function () {
                function getHash(t) {
                    let e = (new TextEncoder).encode(t);
                    return window.crypto.subtle.digest("SHA-1", e)
                }
                function hexString(t) {
                    return [...new Uint8Array(t)].map(t => t.toString(16).padStart(2, "0")).join("")
                }
                $scope.logo = hexString(await getHash(randomString(4)))
            }
            $scope.generate()

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
                    if ($scope.stageIndex < $scope.stages.length - 1){
                        $scope.startLaunching()
                    } else {
                        $scope.close()
                        showSuccessDialog("Token " + $scope.formatTicker($scope.domain) + " launched", success)
                    }
                }, 3000)
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}
