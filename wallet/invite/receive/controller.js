function openInviteReceive(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/invite/receive/index.html",
        controller: function ($scope, $mdBottomSheet) {
            wallet.auth(function (username) {
                var invite_id = getString("invite_id")
                var invite_key = getString("invite_key")
                postContractWithGas(domain, data10.bonus_receive, {
                    invite_id: invite_id,
                    invite_key: invite_key,
                    to_address: username,
                }, function (response) {
                    showSuccessDialog("Received " + response.amount + " " + response.domain)
                })
            })
        }
    }).then(function () {
        if (success)
            success()
    })
}