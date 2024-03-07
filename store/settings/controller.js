function openSettings(domain, success) {
    window.$mdDialog.show({
        templateUrl: '/store/settings/index.html',
        controller: function ($scope) {
            addFormats($scope)

            $scope.title = ""
            $scope.description = ""
            $scope.category = "sandbox"

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

            $scope.uploadArchive = function () {
                selectFile(function (file) {
                    postContractWithGas("store", "api/upload.php", {
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
                postContractWithGas("store", "api/archive.php", {
                    domain: $scope.domain,
                }, function () {
                    showSuccess("Archived successfully")
                })
            }

            $scope.$watch('domain', function (newValue) {
                if (newValue == null) return
                post("/store/api/apps.php", {
                    search_text: (newValue || ""),
                }, function (response) {
                    var apps = response.apps || {}
                    if (apps[newValue] != null){
                        $scope.title = apps[newValue].title
                        $scope.description = apps[newValue].description
                        $scope.category = apps[newValue].category
                    }
                    $scope.$apply()
                })
            })

            $scope.domain = domain


            $scope.selectCategory = function (category) {
                $scope.category = category
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}