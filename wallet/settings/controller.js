function openAppSettings(domain, success) {
    window.$mdDialog.show({
        templateUrl: '/wallet/settings/index.html',
        controller: function ($scope) {
            addFormats($scope)

            $scope.title = ""
            $scope.hide_in_store = false
            $scope.domain = domain

            $scope.save = function () {
                postContractWithGas("wallet", "api/info_update.php", function (key, next_hash) {
                    return {
                        domain: $scope.domain,
                        password: key,
                        next_hash: next_hash,
                        title: $scope.title,
                        hide_in_store: $scope.hide_in_store ? 1 : 0,
                    }
                }, function () {
                    showSuccess("Updated success", success)
                }, function (message) {
                    showError(message)
                })
            }

            $scope.uploadArchive = function () {
                selectFile(function (file) {
                    postContractWithGas("wallet", "api/upload.php", {
                        domain: $scope.domain,
                        file: file,
                    }, function () {
                        showSuccess("Archive uploaded successfully")
                    })
                }, ".zip")
            }

            $scope.uploadLogo = function () {
                selectFile(function (file) {
                    var zip = new JSZip();
                    zip.file("logo.svg", file);
                    zip.generateAsync({type: "blob"}).then(function (content) {
                        uploadFile($scope.domain, content, function () {
                            showSuccess("Logo uploaded successfully")
                        })
                    });
                }, ".svg")
            }

            $scope.uploadPreview = function () {
                selectFile(function (file) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $scope.imgshow = e.target.result;
                    }
                    reader.readAsDataURL(file);

                    var zip = new JSZip();
                    zip.file("preview.jpg", file);
                    zip.generateAsync({type: "blob"}).then(function (content) {
                        uploadFile($scope.domain, content, function () {
                            showSuccess("Preview uploaded successfully")
                        })
                    });
                }, ".jpg")
            }

            $scope.archive = function () {
                postContractWithGas("wallet", "api/archive.php", {
                    domain: $scope.domain,
                }, function () {
                    showSuccess("Archived successfully")
                })
            }

            function init() {
                postContract("wallet", "api/profile.php", {
                    domain: domain,
                    address: wallet.address(),
                }, function (response) {
                    $scope.title = response.title
                    $scope.hide_in_store = response.hide_in_store
                    $scope.$apply()
                })
            }

            init()
        }
    }).then(function () {
        if (success)
            success()
    })
}