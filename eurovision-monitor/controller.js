controller("eurovision-monitor", function ($scope, $http) {
    $scope.data = [];
    $scope.update = function () {
        $http.post('/eurovision-monitor/statistic.php').then(function (response) {
            $scope.data = response.data;
            console.log(response);
        }, function () {

        });
    }

    $scope.update()

    $scope.views_round = function (views) {
        return views.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    }

    $scope.openYoutube = function (video_id) {
        window.location = "https://youtu.be/" + video_id
    }
})