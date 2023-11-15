function openInviteCopy(domain, amount, invite_id, invite_key, cancel_key, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/invite/copy/index.html",
        controller: function ($scope, $mdBottomSheet) {
            $scope.domain = domain
            $scope.amount = amount
            $scope.link = "http://" + window.location.hostname + "/wallet"
                + "?domain=" + domain
                + "&amount=" + amount
                + "&invite_id=" + invite_id
                + "&invite_key=" + invite_key
            $scope.copy = function () {
                navigator.clipboard.writeText($scope.link)
                showSuccess("Link copied")
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