function openGenerator(domain, success) {
    window.$mdDialog.show({
        templateUrl: '/nft/generator/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            $scope.title = ""
            $scope.description = ""
            $scope.category = "sandbox"
            $scope.count = 100
            var selectedFile = null

            post("/store/api/categories.php", {}, function (response) {
                $scope.categories = response.result
                $scope.$apply()
            })

            $scope.uploadPreview = function () {
                selectFile(function (file) {
                    selectedFile = file
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $scope.imgshow = e.target.result;
                    }
                    reader.readAsDataURL(file);

                }, ".jpg")
            }

            $scope.save = function () {
                var zip = new JSZip();
                zip.file("preview.jpg", selectedFile);
                zip.generateAsync({type: "blob"}).then(function (content) {
                    postContractWithGas("nft", "api/save.php", function (key, next_hash) {
                        return {
                            domain: $scope.domain,
                            title: $scope.title,
                            count: $scope.count,
                            description: $scope.description,
                            collection: $scope.collection,
                            file: content,
                        }
                    }, function () {
                        showSuccess("Updated success", success)
                    }, function (message) {
                        showError(message)
                    })
                });

            }

            $scope.selectCategory = function (category) {
                $scope.category = category
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}