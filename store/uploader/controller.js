function showUploader($http) {
    window.$mdBottomSheet.show({
        templateUrl: '/store/uploader/index.html',
        controller: function ($scope, $mdBottomSheet) {
            post("/store/api/apps.php", {}, function (response) {
                $scope.apps = response.result
            })
            $scope.upload = function (item) {
                wallet.calcKey(wallet.gas_path, function (key, hash, username) {
                    selectFile(function (file) {
                        $http({
                            method: 'POST',
                            url: '/store/api/upload.php',
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