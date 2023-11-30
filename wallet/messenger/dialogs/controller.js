function openDialogs(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/messenger/dialogs/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.logged_in = wallet.isLoggedIn()
            $scope.title = wallet.isLoggedIn() ? wallet.username : "Settings"
            $scope.search_opened = true
            var fcm_token = storage.getString("fcm_token")
            updateToken()
            function updateToken() {
                if (fcm_token != '')
                    postWithGas('/wallet/api/messages/save_token.php', {
                        token: fcm_token,
                    }, function () {
                    }, function () {
                    })
            }

            getDialogs()
            function getDialogs() {
                post('/wallet/api/messages/dialogs.php', {
                    address: wallet.username,
                }, function (response) {
                    $scope.dialogs = response.dialogs
                    $scope.$apply()
                })
            }

            $scope.toggleSearch = function () {
                $scope.search_opened = !$scope.search_opened
                if ($scope.search_opened) {
                    setFocus("messages_search_input")
                }
            }

            $scope.$watch('search_text', function search(newValue) {
                if (newValue == null) return
                post('/wallet/api/messages/search.php', {
                    search_text: (newValue || ""),
                }, function (response) {
                    $scope.dialogs = response.result
                    $scope.$apply()
                })
            })

            $scope.selectToAddress = function (address) {
                openMessages(address, success)
            }

            $scope.back = function () {
                $mdBottomSheet.hide()
            }
        }
    }).then(success)
}