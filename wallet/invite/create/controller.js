function openInvite($rootScope, domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/invite/create/index.html",
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            if (DEBUG) {
                $scope.amount = 1
            }
            $scope.create = function () {
                let invite_create_path = randomString(8)
                wallet.calcKey(domain + "/wallet", function (key, hash, username) {
                    postContractWithGas(domain, contract.bonus_create, {
                        domain: domain,
                        from_address: username,
                        from_key: key,
                        from_next_hash: hash,
                        amount: $scope.amount,
                        invite_hash: wallet.calcStartHash(invite_create_path),
                    }, function () {
                        openInviteCopy(domain,
                            wallet.calcStartKey(invite_create_path),
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