function openSettings(coin, app, success) {
    window.$mdDialog.show({
        templateUrl: '/store/settings/index.html',
        controller: function ($scope) {
            addFormats($scope)

            $scope.domain = ""
            $scope.title = ""
            $scope.description = ""
            $scope.category = "sandbox"

            if (coin) {
                $scope.domain = coin.domain
                $scope.category = coin.category
            }

            if (app) {
                $scope.domain = app.domain
                $scope.title = app.title
                $scope.description = app.description
                $scope.category = app.category
            }

            $scope.save = function () {
                postContractWithGas("store", "api/info_update.php", function (key, next_hash) {
                    return {
                        domain: $scope.domain,
                        password: key,
                        next_hash: next_hash,
                        title: $scope.title,
                        description: $scope.description,
                        category: $scope.category,
                    }
                }, function () {
                    showSuccess("Updated success", success)
                }, function (message) {
                    showError(message)
                })
            }

            post("/store/api/categories.php", {}, function (response) {
                $scope.categories = response.result
                $scope.$apply()
            })

            $scope.upload = function () {
                selectFile(function (file) {
                    postContractWithGas("store", "api/upload.php", {
                        domain: $scope.domain,
                        file: file,
                    } , function () {
                        showSuccess("Uploaded success")
                    })
                })
            }

            $scope.archive = function () {
                postContractWithGas("store", "api/archive.php", {
                    domain: $scope.domain,
                } , function () {
                    showSuccess("Archive success")
                })
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}