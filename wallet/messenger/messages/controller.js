function openMessages(to_address, success) {
    var timer;
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/messenger/messages/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.to_address = to_address
            $scope.send_text = ""
            $scope.messages = []

            var last_messages = ""

            function loadMessages() {
                post('/wallet/api/messages/messages.php', {
                    from_address: wallet.username,
                    to_address: to_address,
                }, function (response) {
                    if (last_messages != JSON.stringify(response.messages)) {
                        last_messages = JSON.stringify(response.messages)
                        $scope.messages = response.messages.reverse()
                        $scope.$apply()
                        var div = document.getElementById("messages_scroll");
                        div.scrollTop = div.scrollHeight;
                    }
                })
            }

            $scope.send = function () {
                postWithGas('/wallet/api/messages/send.php', {
                    to_address: to_address,
                    message: $scope.send_text,
                    token: storage.getString("fcm_token"),
                }, function () {
                    $scope.send_text = ""
                    loadMessages()
                })
            }

            $scope.back = function () {
                $mdBottomSheet.hide()
                if (timer)
                    clearInterval(timer)
            }

            loadMessages()
            timer = setInterval(function () {
                loadMessages()
            }, DEBUG ? 1000 : 5000)

        }
    }).then(function () {
        if (success)
            success()
        if (timer)
            clearInterval(timer)
    })
}