function openInviteCopy(domain, invite_key, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/invite/copy/index.html",
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            $scope.link = location.origin + "/wallet?bonus=" + domain + ":" + invite_key

            $scope.share = function () {
                navigator.share({
                    url: $scope.link,
                    title: "Share link with your friend",
                    text: "Login and get your coins",
                })
            }

            $scope.copy = function () {
                $scope.copyText($scope.link)
                showSuccess("Link copied")
            }

            var qrcode
            setTimeout(function () {
                if (qrcode){
                    qrcode.clear()
                    qrcode.makeCode($scope.link)
                } else {
                    qrcode = new QRCode(document.getElementById("qrcode"), {
                        text: $scope.link,
                        width: 128,
                        height: 128,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                }
            }, 300)
        }
    }).then(function () {
        if (success)
            success()
    })
}