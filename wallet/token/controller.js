function openTokenSettings(domain, success) {
    window.$mdDialog.show({
        templateUrl: '/wallet/token/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.DEBUG = DEBUG

            $scope.save = function () {
                postContractWithGas("wallet", "api/profile_update.php", $scope.profile, function () {
                    showSuccess("Updated success", success)
                }, function (message) {
                    showError(message)
                })
            }

            $scope.uploadLogo = function () {
                selectFile(function (file) {
                    var zip = new JSZip();
                    zip.file("logo.svg", file);
                    zip.generateAsync({type: "blob"}).then(function (content) {
                        uploadFile(domain, content, function () {
                            showSuccess("Logo uploaded successfully")
                        })
                    });
                }, ".svg")
            }



            $scope.getLogo = function () {
                if ($scope.logo == null)
                    return "/" + domain + "/logo.svg"
                return $scope.genLogo($scope.logo)
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
                $scope.$apply()
            }

            $scope.saveLogo = function () {
                var zip = new JSZip();
                zip.file("logo.svg", $scope.genSvg($scope.logo));
                zip.generateAsync({type: "blob"}).then(function (content) {
                    uploadFile(domain, content, function () {
                        showSuccess("Logo uploaded successfully")
                    })
                });
            }

            $scope.changePass = function () {
                postContractWithGas("wallet", "api/change_pass.php", {
                    domain: 'data',
                    address: 'admin',
                    password: 'pass',
                })
            }

            function init() {
                post("/wallet/api/profile.php", {
                    domain: domain
                }, function (response) {
                    $scope.profile = response
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