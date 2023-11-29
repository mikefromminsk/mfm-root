function openMessages(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/messages/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.logged_in = wallet.isLoggedIn()
            $scope.title = wallet.isLoggedIn() ? wallet.username : "Settings"
            $scope.send_text = ""
            $scope.fcm_token = storage.getString("fcm_token")
            $scope.messages = []

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

            $scope.updateToken = function () {
                postWithGas('/wallet/api/messages/save_token.php', {
                    token: $scope.fcm_token,
                }, function (response) {
                    $scope.fcm_token = response.success ? "" : $scope.fcm_token
                    showSuccess("Token updated")
                    $scope.$apply()
                })
            }

            $scope.selectToAddress = function (address) {
                $scope.to_address = address
                loadMessages()
                setFocus("messages_send_input")
            }

            function loadMessages(){
                post('/wallet/api/messages/messages.php', {
                    from_address: wallet.username,
                    to_address: $scope.to_address,
                }, function (response) {
                    $scope.messages = response.messages
                    $scope.$apply()
                })
            }

            $scope.back = function () {
                if ($scope.to_address != null) {
                    $scope.to_address = null
                    getDialogs()
                } else
                    $mdBottomSheet.hide()
            }

            $scope.send = function () {
                postWithGas('/wallet/api/messages/send.php', {
                    to_address: $scope.to_address,
                    random_id: randomString(8),
                    prev_id: $scope.messages.length != 0 ? $scope.messages[$scope.messages.length - 1].id : "",
                    message: $scope.send_text,
                    token: $scope.fcm_token,
                }, function () {
                    loadMessages()
                })
            }


            if (DEBUG) {
                $scope.selectToAddress("user")
            }
        }
    }).then(success)
}