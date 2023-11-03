function showUploader($http) {
    window.$mdBottomSheet.show({
        templateUrl: '/store/uploader/index.html',
        controller: function ($scope, $mdBottomSheet) {
            post("/store/api/apps", {}, function (response) {
                $scope.apps = response.result
            })

            $scope.upload = function (item) {
                /*post("/store/api/archive.php", {
                    domain: item.domain
                }, function () {

                })*/

                wallet.calckey(wallet.GAS_PATH, function (key, hash, username, password) {
                    selectFile(function (file) {
                        $http({
                            method: 'POST',
                            url: '/store/api/upload',
                            headers: {
                                'Content-Type': undefined
                            },
                            data: {
                                domain: item.domain,
                                file: file,
                                gas_address: username,
                                gas_key: key,
                                gas_next_hash: hash,
                            },
                            transformRequest: objectToForm
                        }).then(function (response) {
                            console.log(response)
                            $mdBottomSheet.hide()
                        })
                    })
                })

            }
        }
    }).then(function (value) {
    })
}