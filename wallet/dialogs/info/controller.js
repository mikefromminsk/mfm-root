function showInfoDialog(message, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/dialogs/info/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.message = message
        }
    }).then(function () {
        if (success)
            success()
    })
}

function hasToken(domain, success, error) {
    postContract(domain, "api/token/wallet.php", {
        address: wallet.address()
    }, function (response) {
        if (success)
            success(response)
    }, function () {
        showInfoDialog("You need to add " + domain.toUpperCase() + " token", error)
    })
}

function hasBalance(domain, success, error) {
    hasToken(domain, function (response) {
        if (response.amount == null || response.amount == 0) {
            showInfoDialog("You need to buy " + domain.toUpperCase() + " token", error)
        } else {
            if (success)
                success()
        }
    })
}