function openTransactions(domain) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/trans/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            $scope.username = wallet.address()
            post("/wallet/api/trans.php", {
                domain: domain,
                address: wallet.address(),
            }, function (response) {
                $scope.trans = response.trans
            })
        }
    })
}