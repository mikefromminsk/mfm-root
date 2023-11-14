function openInvite(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/invite/create/index.html",
        controller: function ($scope, $mdBottomSheet) {
            $scope.domain = domain
            if (DEBUG) {
                $scope.amount = 100
            }
            $scope.create = function () {
                var invite_id = rand(8);
                let invite_create_path = domain + "/invite/" + invite_id + "/create"
                let invite_cancel_path = domain + "/invite/" + invite_id + "/cancel"
                wallet.calcKey(domain + "/wallet", function (key, hash, username) {
                    postContractWithGas(domain, data10.bonus_create, {
                        domain: domain,
                        invite_id: invite_id,
                        from_address: username,
                        from_key: key,
                        from_next_hash: hash,
                        amount: $scope.amount,
                        invite_hash: wallet.calcStartHash(invite_create_path),
                        cancel_hash: wallet.calcStartHash(invite_cancel_path),
                    }, function () {
                        openInviteCopy(domain,
                            $scope.amount,
                            invite_id,
                            wallet.calcStartKey(invite_create_path),
                            wallet.calcStartKey(invite_cancel_path),
                            success)
                    })
                })
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}