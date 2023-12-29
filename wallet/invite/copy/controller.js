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
        }
    }).then(function () {
        if (success)
            success()
    })
}