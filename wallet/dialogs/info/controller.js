function showInfoDialog(message, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/dialogs/info/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.message = message
            $scope.close = function () {
                $mdBottomSheet.hide()
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}

function hasToken(domain, success) {
    wallet.auth(function (username) {
        postContract(domain, contract.wallet, {
            address: username
        }, function (response) {
            if (success)
                success(response)
        }, function () {
            showInfoDialog("You need to find " + domain.toUpperCase() + " token", function () {
                animateFocus("search_wrapper")
            })
        })
    })
}

function hasBalance(domain, success) {
    hasToken(domain, function (response) {
        if (response.amount == null || response.amount == 0) {
            showInfoDialog("You need to buy " + domain.toUpperCase() + " token", function () {
                animateFocus(domain + "_id")
            })
        } else {
            if (success)
                success()
        }
    })
}