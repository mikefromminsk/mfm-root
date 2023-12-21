function openInviteCopy(domain, invite_key, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/invite/copy/index.html",
        controller: function ($scope, $mdBottomSheet) {
            $scope.domain = domain
            var host = window.location.hostname
            if (DEBUG) {
                host = "192.168.100.7";
            }
            $scope.link = window.location.protocol + "//" + host + "?bonus=" + domain + ":" + invite_key
            $scope.copy = function () {
                if (window.NativeAndroid != null) {
                    window.NativeAndroid.share($scope.link)
                } else {
                    document.getElementById("link_input").focus();
                    document.getElementById("link_input").select();
                    document.execCommand("copy");
                    showSuccess("Link copied")
                }
                $mdBottomSheet.hide()
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}