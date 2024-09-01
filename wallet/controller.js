var subscriptions = {}

function subscribe(channel, callback) {
    if (subscriptions[channel] == null)
        subscriptions[channel] = []
    subscriptions[channel].push(callback)
    if (window.conn != null && subscriptions[channel].length == 1)
        window.conn.send(JSON.stringify({channel: channel}))
}

function main($scope, $http, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    addTokens($scope)

    if (getParam("bonus") != null) {
        openShareReceive(getParam("bonus"))
    }

    $scope.selectTab = function (tab) {
        $scope.selectedIndex = tab
        if (tab == 0) {
            //wallet
            addTokens($scope)
        } else if (tab == 1) {
            //store
            addStore($scope)
        } else if (tab == 2) {
            //transactions
            addTransactions($scope)
        }
    }

    if (window.WebSocket) {
        if (document.location.protocol === "https:") {
            window.conn = new WebSocket("wss://" + document.location.host + ":8887")
        } else {
            window.conn = new WebSocket("ws://" + document.location.host + ":8887")
        }
        window.conn.onopen = function () {
            for (var channel of Object.keys(subscriptions))
                window.conn.send(JSON.stringify({channel: channel}))
        }
        window.conn.onmessage = function (evt) {
            var message = JSON.parse(evt.data)
            for (let callback of subscriptions[message.channel]) {
                callback(message)
            }
        }
    }

}