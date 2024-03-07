function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.selectTab = function (tab) {
        if ($scope.selectedIndex == tab) {
            for (frame of document.querySelectorAll('iframe'))
                frame.contentWindow.location.reload();
        } else {
            $scope.selectedIndex = tab
        }
    }

}