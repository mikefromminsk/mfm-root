function openWeb(link, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/web/index.html',
        controller: function ($scope) {
            addFormats($scope)
            setTimeout(function () {
                document.getElementById('web-frame').src = link
            }, 1)
        }
    }).then(function () {
        if (success)
            success()
    })
}