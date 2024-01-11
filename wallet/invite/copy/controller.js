function openInviteCopy(domain, invite_key, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/invite/copy/index.html",
        controller: function ($scope, $mdBottomSheet) {
            $scope.domain = domain
            $scope.link = location.origin + "?bonus=" + domain + ":" + invite_key
            $scope.copy = function () {
                document.getElementById("link_input").focus();
                document.getElementById("link_input").select();
                document.execCommand("copy");
                showSuccess("Link copied")
                $mdBottomSheet.hide()
            }
            $scope.share = function () {
                navigator.share({
                    url: $scope.link,
                    title: "Share link with your friend",
                    text: "Login and get your coins",
                })
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