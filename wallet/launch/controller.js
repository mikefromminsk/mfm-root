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
            $scope.launch = function () {
                postWithGas("/wallet/api/launch.php", {
                    domain: $scope.domain
                }, function () {
                    showSuccess("Contract uploaded")
                    postContractWithGas($scope.domain, data10.init, {
                        address: wallet.username,
                        next_hash: md5(wallet.calchashStart($scope.domain + "/wallet")),
                    }, function () {
                        showSuccess("Token " + $scope.domain + " launched")
                        wallet.domainAdd($scope.domain)
                        $mdBottomSheet.hide()
                    })
                })
            }


            /*
            $scope.download = function () {
                downloadFile("/store/api/gas.zip")
            }
            $scope.upload = function () {
                wallet.calckey(wallet.GAS_PATH, function (key, hash, username, password) {
                    selectFile(function (file) {
                        function makeTextFile(text) {
                            var textFile = null;
                            var data = new Blob([text], {type: 'text/plain'});
                            if (textFile !== null)
                                window.URL.revokeObjectURL(textFile);
                            textFile = window.URL.createObjectURL(data);
                            return textFile;
                        }
                        postForm('/wallet/api/testUploading.php', {
                            file: new Blob(["This is a sample file content."], {
                                type: "text/plain;charset=utf-8",
                            })
                        }, function () {
                            showSuccess("success")
                        }, function () {
                            showError("error")
                        })
                        $http({
                            method: 'POST',
                            url: ,
                            headers: {
                                'Content-Type': undefined
                            },
                            data: {
                                domain: $scope.domain,
                                file: makeTextFile("ssdss"),
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
            }*/
        }
    }).then(function () {
        success()
    })
}