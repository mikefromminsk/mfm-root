function openInviteCopy(domain, invite_key, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/invite/copy/index.html",
        controller: function ($scope, $mdBottomSheet) {
            $scope.domain = domain
            $scope.link = window.location.protocol + "//" + window.location.hostname + "?bonus=" + domain + ":" + invite_key
            $scope.copy = function () {
                if (window.NativeAndroid != null) {
                    window.NativeAndroid.share($scope.link)
                } else {
                    document.getElementById("link_input").focus();
                    document.getElementById("link_input").select();
                    document.execCommand("copy");
                    showSuccess("Link copied")
                }
            }
            $scope.close = function () {
                $mdBottomSheet.hide()
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}