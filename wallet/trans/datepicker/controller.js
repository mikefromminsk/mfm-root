function selectDate(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/trans/datepicker/index.html',
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            var date = new Date()
            date.setHours(0)
            date.setMinutes(0)
            date.setSeconds(0)

            $scope.day = date.getDate()
            $scope.month = date.getMonth()
            $scope.year = date.getFullYear()

            $scope.addDay = function () {
                date.setDate(date.getDate() + 1)
                $scope.day = date.getDate()
            }
            $scope.minusDay = function () {
                date.setDate(date.getDate() - 1)
                $scope.day = date.getDate()
            }

            $scope.addMonth = function () {
                date.setMonth(date.getMonth() + 1)
                $scope.month = date.getMonth()
            }
            $scope.minusMonth = function () {
                date.setMonth(date.getMonth() - 1)
                $scope.month = date.getMonth()
            }

            $scope.addYear = function () {
                date.setFullYear(date.getFullYear() + 1)
                $scope.year = date.getFullYear()
            }
            $scope.minusYear = function () {
                date.setFullYear(date.getFullYear() - 1)
                $scope.year = date.getFullYear()
            }

            $scope.select = function () {
                if (success)
                    success(date)
                $scope.back()
            }
        }
    })
}