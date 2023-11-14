function openInviteCopy(domain, amount, invite_id, invite_key, cancel_key, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/invite/copy/index.html",
        controller: function ($scope, $mdBottomSheet) {
            $scope.domain = domain
            $scope.amount = amount
            // grid link hostname
            $scope.amount = "http://" + window.location.hostname
                + "?domain=" + domain
                + "&amount=" + amount
                + "&invite_id=" + invite_id
                + "&invite_key=" + invite_key
        }
    }).then(function () {
        if (success)
            success()
    })
}