function openLaunchDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/launch/index.html",
        locals: {
            domain: domain
        },
        controller: function ($scope, $mdBottomSheet, $http, locals) {
            $scope.domain = locals.domain
            $scope.amount = 1000000
            if (DEBUG) {
                $scope.domain = "super"
            }
            $scope.download = function () {
                downloadFile("/store/api/gas.zip")
            }
            $scope.upload = function () {
                wallet.calckey(wallet.GAS_PATH, function (key, hash, username, password) {
                    selectFile(function (file) {
                        $http({
                            method: 'POST',
                            url: '/store/api/upload.php',
                            headers: {
                                'Content-Type': undefined
                            },
                            data: {
                                domain: $scope.domain,
                                file: file,
                                gas_address: username,
                                gas_key: key,
                                gas_next_hash: hash,
                            },
                            transformRequest: objectToForm
                        }).then(function (response) {
                            postContractWithGas($scope.domain, data10.init, {
                                address: username,
                                next_hash: md5(wallet.calchashStart($scope.domain + "/wallet")),
                            }, function () {
                                showSuccess("Token " + $scope.domain + " launched")
                                wallet.domainAdd($scope.domain)
                                $mdBottomSheet.hide()
                            })
                        })
                    })
                })
            }
        }
    }).then(function () {
        success()
    })
}